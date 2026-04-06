<?php

namespace App\Models\Traits;

use App\Models\Taxonomy\TaxonNameUsageMap;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasUsages
 *
 * This trait can be used by any model that represents a taxonomic name (e.g., TaxonName, Synonym, etc.)
 * to define a relationship to the TaxonNameUsageMap model, which captures all the instances where 
 * this name has been cited or used in literature.
 */
trait HasUsages
{
    /**
     * Get all instances where this name (in any of its roles) 
     * has been cited or used in literature.
     * 
     * @return HasMany
     */
    public function usages(): HasMany
    {
        // Since all your name view-models share the base 'id' 
        // from the taxon_names table, this relationship remains consistent.
        return $this->hasMany(TaxonNameUsageMap::class, 'taxon_name_id');
    }
}