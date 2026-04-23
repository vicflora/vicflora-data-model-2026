<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;


/**
 * Interface Limitable
 * 
 * This interface is for models that have a polymorphic
 * relationship to a "limitable" entity. The limitable entity can be any model
 * that implements the necessary fields (limitable_type and limitable_id).
 */
interface limitable
{
    /**
     * Get the limitable entity. This will return the related model based on the
     * limitable_type and limitable_id fields.
     * @return MorphTo
     */
    public function limitable(): MorphTo;
}
