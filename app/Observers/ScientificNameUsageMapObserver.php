<?php

namespace App\Observers;

use App\Models\Taxonomy\ScientificNameUsageMap;
use App\Models\Shared\ControlledTerm;
use Illuminate\Validation\ValidationException;

/**
 * Manages the referential integrity between Name Usage Roles and Taxon Name types.
 * * Since VicFlora uses a "Sidecar" pattern (ScientificName vs VernacularName),
 * this observer ensures that 'Accepted' or 'Synonym' roles are only assigned 
 * to Scientific Names, and 'Vernacular' roles to Vernacular Names.
 */
class ScientificNameUsageMapObserver
{
    /**
     * Handle the ScientificNameUsageMap "saving" event.
     * * Validates the relationship before it hits the database, allowing us 
     * to safely remove expensive 'whereHas' checks from our Query Builders.
     *
     * @param  ScientificNameUsageMap  $map
     * @return void
     * @throws ValidationException
     */
    public function saving(ScientificNameUsageMap $map): void
    {
        $this->validateRoleMatchesNameType($map);
    }

    /**
     * Internal validation logic using cached ControlledTerm IDs.
     */
    protected function validateRoleMatchesNameType(ScientificNameUsageMap $map): void
    {
        // IDs are resolved once per request via static cache in ControlledTerm
        $acceptedId = ControlledTerm::getIdByCode('NAME_USAGE_ROLE', 'ACCEPTED');
        $synonymId = ControlledTerm::getIdByCode('NAME_USAGE_ROLE', 'SYNONYM');

        // Accesses the 'name_type' virtual attribute from the TaxonName view/model
        $nameType = $map->taxonName->name_type; 

        // Validate Scientific roles
        if (in_array($map->name_usage_role_id, [$acceptedId, $synonymId])) {
            if ($nameType !== 'SCIENTIFIC') {
                throw ValidationException::withMessages([
                    'name_usage_role_id' => "Invalid mapping: Role '{$map->nameUsageRole->label}' requires a Scientific Name, but '{$nameType}' was provided."
                ]);
            }
        }
    }
}