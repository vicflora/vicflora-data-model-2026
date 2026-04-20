<?php

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use App\Models\Traits\IncrementsVersion;

class EntityCreatorMap extends MorphPivot
{
    use Blameable, IncrementsVersion;

    protected $table = 'entity_creator_map';

    public $incrementing = true;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The Agent who is the creator.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * The model that was created (Specimen, Reference, etc.).
     */
    public function createable()
    {
        return $this->morphTo();
    }
}