<?php

namespace App\Models\Shared;

use App\Models\Contracts\HasExternalIdentity;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class EntityIdentityMap
 *
 * Represents a mapping between an internal entity (identified by its type and
 * ID) and an external identity (identified by an external ID). This model is
 * based on the 'entity_identity_map' database table, which captures the
 * relationships between internal entities and their corresponding external
 * identities.
 *
 * The model includes fields for the entity type, entity ID, and external
 * identity ID, as well as relationships to the external identity metadata.
 *
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $external_identity_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read ExternalIdentity|null $externalIdentity
 * @property-read Model|HasExternalIdentity $entity
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'entity_identity_map', key: 'id', incrementing: true)]
#[Fillable([
    'entity_type',
    'entity_id',
    'external_identity_id',
])]
class EntityIdentityMap extends MorphPivot implements HasExternalIdentity
{
    use Auditable;

    /**
     * Link back to the metadata of the external ID.
     */
    public function externalIdentity()
    {
        return $this->belongsTo(ExternalIdentity::class, 'external_identity_id');
    }

    /**
     * Get the internal entity associated with this identity map. This will return
     * the related model based on the entity_type and entity_id fields.
     * @return MorphTo
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}