<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface Linkable
 * 
 * This interface is for models that have a
 * polymorphic relationship to an "external identity" entity. The internal
 * entity can be any model that implements the necessary fields (linkable_type and
 * linkable_id).
 */
interface Linkable
{
    /**
     * Get the internal entity associated with this identity map. This will return
     * the related model based on the linkable_type and linkable_id fields.
     * @return MorphTo
     */
    public function linkable(): MorphTo;
}
