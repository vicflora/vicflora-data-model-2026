<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface HasExternalIdentity This interface is for models that have a
 * polymorphic relationship to an "external identity" entity. The internal
 * entity can be any model that implements the necessary fields (entity_type and
 * entity_id).
 */
interface HasExternalIdentity
{
    /**
     * Get the internal entity associated with this identity map. This will return
     * the related model based on the entity_type and entity_id fields.
     * @return MorphTo
     */
    public function entity(): MorphTo;
}
