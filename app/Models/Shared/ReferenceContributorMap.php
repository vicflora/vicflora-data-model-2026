<?php

namespace App\Models\Shared;

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ReferenceContributorMap
 * 
 * Intersection model linking References to Agents with specific roles and order.
 * 
 * @property int $id
 * @property int $reference_id
 * @property int $agent_id
 * @property int $contributor_role_id
 * @property int $sequence
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read ControlledTerm $role
 * @property-read Agent $agent
 * @property-read Reference $reference
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'reference_contributors_map')]
#[Fillable([
    'reference_id',
    'agent_id',
    'contributor_role_id',
    'sequence',
])]
class ReferenceContributorMap extends Pivot
{
    use Blameable, IncrementsVersion;

    /**
     * Relationship to the Contributor Role term.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'contributor_role_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }
}