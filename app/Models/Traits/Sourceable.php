<?php

namespace App\Models\Traits;

use App\Models\Shared\EntitySourceMap;
use App\Models\Shared\Reference;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Sourceable
{
    /**
     * Get the bibliographic source for this entity.
     * We use morphToMany to access pivot metadata, but the database unique 
     * index ensures there is only ever one.
     */
    public function source(): MorphToMany
    {
        return $this->morphToMany(Reference::class, 'sourceable', 'entity_source_map')
            ->using(EntitySourceMap::class)
            ->withPivot(['id', 'metadata', 'created_by', 'updated_by'])
            ->withTimestamps();
    }

    /**
     * Helper to get the single source record.
     */
    public function getSourceAttribute()
    {
        return $this->source->first();
    }
}