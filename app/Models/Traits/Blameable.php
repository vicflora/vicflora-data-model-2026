<?php

namespace App\Models\Traits;

use App\Models\Taxonomy\Agent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait Blameable
{
    /**
     * Explicitly attribute the record to the Agent who owns the current User.
     */
    public function blame(): self
    {
        if (Auth::check()) {
            // Find the agent where user_id matches the current session
            $agentId = Agent::where('user_id', Auth::id())->value('id');

            if ($agentId) {
                if (!$this->exists) {
                    $this->created_by_id = $agentId;
                }
                $this->updated_by_id = $agentId;
            }
        }
        return $this;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'created_by_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'updated_by_id');
    }
}