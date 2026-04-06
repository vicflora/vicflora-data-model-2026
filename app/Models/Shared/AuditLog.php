<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class AuditLog
 *
 * Represents an audit log entry, which captures changes made to auditable models. 
 * This model is based on the 'audit_logs' database table, which records the 
 * details of changes, including the type of event, the model affected, the user 
 * responsible for the change, and the old and new values.
 *
 * The model includes a polymorphic relationship to the auditable model that was 
 * changed and a relationship to the user who made the change.
 * 
 * @property int $id
 * @property string $event_type
 * @property string $auditable_type
 * @property int $auditable_id
 * @property int|null $user_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * 
 * @property-read Model $auditable
 * @property-read User|null $user
 */
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