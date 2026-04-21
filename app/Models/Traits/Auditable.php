<?php

namespace App\Models\Traits;

use App\Models\Shared\Agent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the trait to handle automatic blaming and versioning.
     */
    public static function bootAuditable(): void
    {
        static::creating(function ($model) {
            $model->applyBlame(true);
        });

        static::updating(function ($model) {
            $model->applyBlame(false);

            // Handle version incrementing if the column exists and model is dirty
            if ($model->isDirty() && isset($model->version)) {
                $model->version++;
            }
        });
    }

    /**
     * Internal logic to attribute the record to the Agent tied to the User.
     */
    protected function applyBlame(bool $isCreating): void
    {
        if (Auth::check()) {
            // In your system, we blame the Agent (Mueller, Walsh, etc.) 
            // rather than the raw User account.
            $agentId = Agent::where('user_id', Auth::id())->value('id');

            if ($agentId) {
                if ($isCreating) {
                    $this->created_by_id = $agentId;
                }
                $this->updated_by_id = $agentId;
            }
        }
    }

    /**
     * The Agent who originally created the record in the system.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'created_by_id');
    }

    /**
     * The Agent who most recently updated the record in the system.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'updated_by_id');
    }
}