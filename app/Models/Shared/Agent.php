<?php

namespace App\Models\Shared;

use App\Models\Traits\Auditable;
use App\Models\Traits\Linkable;
use App\Observers\AgentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Agent
 *
 * Represents an agent, which can be a person or organization involved in
 * various roles within the application. This model is based on the 'agents'
 * database table, which captures information about individuals and
 * organizations that interact with the system.
 *
 * The model includes relationships to the agent type (ControlledTerm) and the
 * associated Laravel User account. It also includes a "smart" attribute to get
 * the label of the agent type directly and a static helper method to retrieve
 * all valid agent types for use in dropdown menus.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $agent_type_id
 * @property string $name
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $initials
 * @property string|null $email
 * @property string|null $legal_name
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read ControlledTerm|null $agentType
 * @property-read User|null $user
 * @property-read Collection<int, Reference> $references
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(
    name: 'agents', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'user_id',
    'agent_type_id',
    'name',
    'first_name',
    'last_name',
    'initials',
    'email',
    'legal_name',
])]
#[ObservedBy(AgentObserver::class)]
class Agent extends Model
{
    use Auditable, Linkable;

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
     * The references associated with this agent.
     */
    public function references(): BelongsToMany
    {
        return $this->belongsToMany(Reference::class, 'reference_contributors_map')
                    ->withPivot('contributor_role_id', 'sequence');
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

    /**
     * For Short Citations: "Smith" or "RBG Victoria"
     * Usage: $agent->short_name
     */
    protected function shortName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->last_name ?? $this->name,
        );
    }

    /**
     * For Full Bibliographies: "Smith, J. S." or "RBG Victoria"
     * Usage: $agent->full_bibliographic_name
     */
    protected function fullBibliographicName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->last_name && $this->initials) {
                    return "{$this->last_name}, {$this->initials}";
                }
                return $this->name;
            }
        );
    }
}