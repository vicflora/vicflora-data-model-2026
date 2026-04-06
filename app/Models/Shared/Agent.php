<?php

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Agent
 *
 * Represents an agent, which can be a person or organization involved in various roles within the application. This model is based on the 'agents' database table, which captures information about individuals and organizations that interact with the system.
 *
 * The model includes relationships to the agent type (ControlledTerm) and the associated Laravel User account. It also includes a "smart" attribute to get the label of the agent type directly and a static helper method to retrieve all valid agent types for use in dropdown menus.
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $agent_type_id
 * @property string $name
 * @property string|null $email
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read ControlledTerm|null $agentType
 * @property-read User|null $user
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
 */
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
    use Blameable, IncrementsVersion;

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