<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface Sourceable This interface is for models that have a polymorphic
 * relationship to a "sourceable" entity. The sourceable entity can be any model
 * that implements the necessary fields (sourceable_type and sourceable_id).
 */
interface Sourceable
{
    /**
     * Get the sourceable entity. This will return the related model (e.g.,
     * NomenclaturalType, TaxonConceptMapping) based on the sourceable_type and
     * sourceable_id fields.
     * @return MorphTo
     */
    public function sourceable(): MorphTo;
}
