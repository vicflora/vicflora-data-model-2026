<?php

namespace App\Models\Shared;

use App\Models\Contracts\Createable;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class EntityCreatorMap
 *
 * Links an Agent (creator) to a createable entity (e.g., Specimen, Reference)
 * with optional metadata.
 *
 * @property int $id
 * @property int $agent_id
 * @property string $createable_type
 * @property int $createable_id
 * @property array|null $metadata
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read Agent $agent
 * @property-read Model|Createable $createable
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'entity_creator_map', key: 'id', incrementing: true)]
#[Fillable([
    'id',
    'agent_id',
    'createable_type',
    'createable_id',
    'metadata',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class EntityCreatorMap extends MorphPivot implements Createable
{
    use Auditable;

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
     * @return MorphTo
     */
    public function createable(): MorphTo
    {
        return $this->morphTo();
    }
}