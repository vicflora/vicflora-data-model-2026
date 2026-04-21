<?php

namespace App\Models\Geography;

use App\Models\Shared\Reference;
use App\Models\Traits\IsSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ThreatStatusAuthority
 *
 * Represents a threat status authority, which is a reference that describes the
 * authority for a threat status. This model is based on the
 * 'threat_status_authorities' database view, which combines data from the
 * 'references' table and its related extension for threat status authorities.
 *
 * The model includes relationships to the base Reference model and any sidecar
 * data.
 *
 * @property int $id
 * @property string|null $code
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Reference $reference
 */
#[Table(
    name: 'threat_status_authorities_ext', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'code',
])]
class ThreatStatusAuthority extends Model
{
    use IsSidecar;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the reference that this threat status authority belongs to.
     * 
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'id');
    }

    public function getSidecarFields(): array
    {
        return ['code'];
    }
}