<?php

namespace App\Models\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Taxonomy\Agent;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;

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