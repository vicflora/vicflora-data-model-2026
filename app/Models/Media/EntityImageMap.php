<?php

namespace App\Models\Media;

use App\Models\Contracts\HasImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class EntityImageMap
 *
 * Links an image to a model that has images.
 * 
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $image_id
 * @property int|null $image_role_id
 * @property int|null $sort_order
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read Image $image
 * @property-read ControlledTerm|null $imageRole
 * @property-read Model|HasImage $entity
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'entity_image_map', key: 'id', incrementing: true)]
#[Fillable([
    'entity_type',
    'entity_id',
    'image_id',
    'image_role_id',
    'sort_order',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class EntityImageMap extends MorphPivot implements HasImage
{
    use Auditable;

    /**
     * Get the image associated with the map.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Get the role (caption, hero, thumbnail, etc.) of the image.
     */
    public function imageRole(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'image_role_id');
    }

    /**
     * Get the internal entity associated with this image map. This will return
     * the related model based on the entity_type and entity_id fields.
     * @return MorphTo
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}