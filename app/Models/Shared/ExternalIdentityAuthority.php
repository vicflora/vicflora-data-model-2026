<?php

namespace App\Models\Shared;

use App\Models\Traits\IsSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ExternalIdentityAuthority
 *
 * Represents an external identity authority, which is a reference that can be
 * used to validate external identities. This model is based on the
 * 'external_identity_authorities' database view, which combines data from the
 * 'references' table and its related extension for external identity
 * authorities.
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
    name: 'external_identity_authorities_ext', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'code',
])]
class ExternalIdentityAuthority extends Model
{
    use IsSidecar;

    /**
     * Get sidecar fields
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return [];
    }

    /**
     * Get the reference that this external identity authority belongs to.
     * 
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'id');
    }
}