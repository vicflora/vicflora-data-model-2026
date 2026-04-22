<?php

namespace App\Observers;

use App\Models\Taxonomy\VernacularNameUsageMap;
use Illuminate\Validation\ValidationException;

/**
 * Manages the referential integrity between Name Usage Roles and Taxon Name types.
 * * Since VicFlora uses a "Sidecar" pattern (ScientificName vs VernacularName),
 * this observer ensures that 'Accepted' or 'Synonym' roles are only assigned 
 * to Scientific Names, and 'Vernacular' roles to Vernacular Names.
 */
class VernacularNameUsageMapObserver
{
    /**
     * Handle the VernacularNameUsageMap "saving" event.
     * * Validates the relationship before it hits the database, allowing us 
     * to safely remove expensive 'whereHas' checks from our Query Builders.
     *
     * @param  VernacularNameUsageMap  $map
     * @return void
     * @throws ValidationException
     */
    public function saving(VernacularNameUsageMap $map): void
    {
        $this->validateRoleMatchesNameType($map);
    }

    /**
     * Internal validation logic using cached ControlledTerm IDs.
     */
    protected function validateRoleMatchesNameType(VernacularNameUsageMap $map): void
    {
        // Accesses the 'name_type' virtual attribute from the TaxonName view/model
        $nameType = $map->taxonName->name_type; 

        if ($nameType !== 'VERNACULAR') {
            throw ValidationException::withMessages([
                'name_usage_role_id' => "Invalid mapping: Role 'Vernacular Name' requires a Vernacular Name sidecar, but '{$nameType}' was provided."
            ]);
        }
    }
}