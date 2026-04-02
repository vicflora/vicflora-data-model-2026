<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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