<?php

namespace App\Models\Mapper;

use App\Models\Shared\Agent as Agent;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class Assertion
 *
 * Represents an assertion about an occurrence, made by an expert or system. 
 * This model captures the details of the assertion, including the term being 
 * asserted, the value of the assertion, the reason for the assertion, and any 
 * remarks.
 *
 * The model includes relationships to the Occurrence that the assertion is 
 * about and the Agent (expert/system) making the assertion.
 * 
 * @property int $id
 * @property string $guid
 * @property int $occurrence_id
 * @property string $term
 * @property string $asserted_value
 * @property string|null $reason
 * @property string|null $remarks
 * @property int $agent_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Occurrence $occurrence
 * @property-read Agent $agent
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
 */
#[Table(name: 'public.assertions', primaryKey: 'id', incrementing: true)]
#[Fillable([
    'guid', 'occurrence_id', 'term', 'asserted_value', 
    'reason', 'remarks', 'agent_id'
])]
class Assertion extends Model
{
    use HasUuids, Blameable, IncrementsVersion;

    /**
     * Specify the UUID column name.
     */
    public function uniqueIds(): array
    {
        return ['guid'];
    }

    /**
     * Link to the occurrence (across schemas).
     */
    public function occurrence(): BelongsTo
    {
        return $this->belongsTo(Occurrence::class, 'occurrence_id');
    }

    /**
     * The expert/system making the claim.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}