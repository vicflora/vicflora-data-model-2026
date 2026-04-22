<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface Createable This interface is for models that have a polymorphic
 * relationship to a "createable" entity. The createable entity can be any model
 * that implements the necessary fields (createable_type and createable_id).
 */
interface Createable
{
    /**
     * The model that was created (Specimen, Reference, etc.).
     * @return MorphTo
     */
    public function createable(): MorphTo;
}
