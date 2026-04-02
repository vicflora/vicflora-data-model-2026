<?php

namespace App\Models\Traits;

use App\Models\Taxonomy\TaxonNameUsageMap;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasUsages
{
    /**
     * Get all instances where this name (in any of its roles) 
     * has been cited or used in literature.
     */
    public function usages(): HasMany
    {
        // Since all your name view-models share the base 'id' 
        // from the taxon_names table, this relationship remains consistent.
        return $this->hasMany(TaxonNameUsageMap::class, 'taxon_name_id');
    }
}