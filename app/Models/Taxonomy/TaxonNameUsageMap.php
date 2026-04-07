<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class TaxonNameUsageMap
 *
 * Represents a mapping of taxonomic name usages within taxonomic concepts.
 * This model is based on the 'taxon_name_usages_map' database table, which 
 * captures the application of taxonomic names to taxonomic concepts, along with
 * the role of the name usage (e.g., accepted name, synonym).
 *
 * The model includes relationships to the TaxonName being used, the TaxonConcept
 * to which it is applied, and the role of the name usage defined by a 
 * ControlledTerm from the NAME_USAGE_ROLE vocabulary.
 * 
 * @property int $id
 * @property int $taxon_name_id
 * @property int $taxon_concept_id
 * @property int $name_usage_role_id
 * @property bool $is_preferred_vernacular_name
 * @property string|null $country_code
 * @property string|null $remarks
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read TaxonName $taxonName
 * @property-read TaxonConcept $taxonConcept
 * @property-read ControlledTerm $nameUsageRole
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
#[Table(
    name: 'taxon_name_usages_map', 
    key: 'id',
    incrementing: true
)]
#[Fillable([
    'created_at',
    'updated_at',
    'taxon_name_id',
    'taxon_concept_id',
    'name_usage_role_id',
    'is_preferred_vernacular_name',
    'country_code',
    'remarks',    
    'created_by_id',
    'updated_by_id',
])]
class TaxonNameUsageMap extends Model
{
    use Blameable;

    /**
     * The Name being used.
     */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class);
    }

    /**
     * The TaxonConcept to which the name was applied.
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class);
    }

    /**
     * The role of the name usage, e.g., ACCEPTED, SYNONYM.
     */
    public function nameUsageRole(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'name_usage_role_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'NAME_USAGE_ROLE');
            });
    }
}