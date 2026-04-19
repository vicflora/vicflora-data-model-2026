<?php

namespace App\Models\Traits;

use App\Models\Media\EntityImageMap;
use App\Models\Media\Image;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasImages
{
    /**
     * Get all images associated with the model.
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(
            Image::class,
            'entity',
            'entity_image_map'
        )
        ->using(EntityImageMap::class)
        ->withPivot(['image_role_id', 'sort_order'])
        ->withTimestamps();
    }
}
