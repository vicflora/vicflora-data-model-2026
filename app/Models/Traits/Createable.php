<?php

namespace App\Models\Traits;

use App\Models\Shared\Agent;
use App\Models\Shared\EntityCreatorMap;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Createable
{
    /**
     * Get the historical creator (Agent) for this entity.
     * * Since we need pivot data (created_at_date, metadata), we use morphToMany 
     * but name it singular to reflect the business logic of a single creator.
     */
    public function creator()
    {
        return $this->morphToMany(Agent::class, 'createable', 'entity_creator_map')
            ->using(EntityCreatorMap::class)
            ->withPivot(['id', 'created_at_date', 'metadata', 'created_by', 'updated_by'])
            ->withTimestamps();
    }

    /**
     * Helper to get the single creator record.
     */
    public function getCreatorAttribute()
    {
        return $this->creator->first();
    }
}