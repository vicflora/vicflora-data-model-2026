<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table(
    name: 'audit_logs', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'event_type',
    'auditable_type',
    'auditable_id',
    'user_id',
    'old_values',
    'new_values',
    'ip_address',
    'user_agent',
    'created_at'
])]
#[WithoutTimestamps]
class AuditLog extends Model
{
    // Use the method for casting until the #[Casts] attribute arrives
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}