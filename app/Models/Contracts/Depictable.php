<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface Depictable This interface is for models that have a polymorphic
 * relationship to a "depictable" entity. The depictable entity can be any model
 * that implements the necessary fields (depictable_type and depictable_id).
 */
interface Depictable
{
    /**
     * Get the internal entity associated with this image map. This will return
     * the related model based on the depictable_type and depictable_id fields.
     * @return MorphTo
     */
    public function depictable(): MorphTo;
}
