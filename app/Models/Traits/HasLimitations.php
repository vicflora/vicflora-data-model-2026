<?php

namespace App\Models\Traits;

use App\Models\Glossary\Limitation;
use App\Models\Glossary\GlossaryLimitationMap;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasLimitations
{
    /**
     * Get all limitations associated with the model.
     * * This uses the polymorphic 'limitable' relationship 
     * defined in the glossary_limitation_map bridge.
     */
    public function limitations(): MorphToMany
    {
        return $this->morphToMany(
            Limitation::class,
            'limitable', // This matches the $table->morphs('limitable') in your migration
            'glossary_limitation_map'
        )
        ->using(GlossaryLimitationMap::class)
        ->withTimestamps();
    }

    /**
     * Scope a query to only include entities that have a specific limitation.
     */
    public function scopeWhereHasLimitation($query, $limitationName)
    {
        return $query->whereHas('limitations', function ($q) use ($limitationName) {
            $q->where('name', $limitationName);
        });
    }
}