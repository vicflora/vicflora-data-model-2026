<?php

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table(name: 'entity_identity_map')]
class EntityIdentityMap extends MorphPivot
{
    use Blameable;

    /**
     * Set to true because we added $table->id() to the migration.
     */
    public $incrementing = true;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'external_identity_id',
    ];

    /**
     * Link back to the metadata of the external ID.
     */
    public function externalIdentity()
    {
        return $this->belongsTo(ExternalIdentity::class, 'external_identity_id');
    }
}