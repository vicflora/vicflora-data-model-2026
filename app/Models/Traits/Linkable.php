<?php

namespace App\Models\Traits;

use App\Models\Shared\ExternalIdentity;
use App\Models\Shared\EntityIdentityMap;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Linkable
{
    /**
     * Get all external identities for this entity.
     */
    public function externalIdentities(): MorphToMany
    {
        return $this->morphToMany(
            ExternalIdentity::class,
            'entity',
            'entity_identity_map'
        )
        ->using(EntityIdentityMap::class) // Your custom pivot with standard_form
        ->withPivot(['metadata'])
        ->withTimestamps();
    }
    
    /**
     * Helper to get a specific identity by authority code.
     */
    public function getIdentity(string $authorityCode)
    {
        return $this->externalIdentities()
            ->whereHas('authority', fn($q) => $q->where('code', $authorityCode))
            ->first();
    }
}