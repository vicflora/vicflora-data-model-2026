<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface HasImage This interface is for models that have a polymorphic
 * relationship to an "image" entity. The 'has image' model can be any model
 * that implements the necessary fields (entity_type and entity_id).
 */
interface HasImage
{
    /**
     * Get the internal entity associated with this image map. This will return
     * the related model based on the entity_type and entity_id fields.
     * @return MorphTo
     */
    public function entity(): MorphTo;
}
