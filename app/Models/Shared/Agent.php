<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(
    name: 'agents', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'user_id',
    'agent_type_id',
    'name',
    'email',
])]
class Agent extends Model
{
    /**
     * Relationship: Scoped to the AGENT_TYPE vocabulary.
     */
    public function agentType(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'agent_type_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'AGENT_TYPE');
            });
    }

    /**
     * Relationship: The Laravel User account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The "Smart" Attribute: Get the label of the agent type directly.
     * Usage: $agent->type_label
     */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->agentType?->label ?? 'Unknown',
        );
    }

    /**
     * Static Helper: Get all valid Agent Types for a dropdown menu.
     * Usage: Agent::getAvailableTypes()
     */
    public static function getAvailableTypes()
    {
        return ControlledTerm::inVocabulary('AGENT_TYPE')
            ->orderBy('sort_order')
            ->get();
    }
}