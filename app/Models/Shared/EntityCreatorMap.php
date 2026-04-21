<?php

namespace App\Models\Shared;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EntityCreatorMap extends MorphPivot
{
    use Auditable;

    protected $table = 'entity_creator_map';

    public $incrementing = true;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The Agent who is the creator.
     * 
     * @return BelongsTo
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * The model that was created (Specimen, Reference, etc.).
     */
    public function createable(): MorphTo
    {
        return $this->morphTo();
    }
}