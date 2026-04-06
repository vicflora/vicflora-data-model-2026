<?php

namespace App\Models\Profile;

use App\Models\Image\Image;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class SpecimenImageMap
 *
 * Represents the mapping between specimens and images, allowing for the association 
 * of multiple images with a single specimen and vice versa. This model is based on 
 * the 'specimen_image_map' database table, which captures the many-to-many 
 * relationship between specimens and images.
 *
 * The model includes fields for the specimen ID, image ID, an optional external ID 
 * for linking to external systems, and a sort order for ordering images associated 
 * with a specimen.
 * 
 * @property int $id
 * @property int $specimen_id
 * @property int $image_id
 * @property string|null $external_id
 * @property int|null $sort_order
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[Table(
    name: 'specimen_image_map',
    key: 'id',
    incrementing: true
)]
#[Fillable([
    'specimen_id',
    'image_id',
    'external_id',
    'sort_order'
])]
class SpecimenImageMap extends Pivot
{
    use Blameable, IncrementsVersion;

    /**
     * Define the relationship to the Specimen model.
     * 
     * @return BelongsTo
     */
    public function specimen(): BelongsTo
    {
        return $this->belongsTo(Specimen::class);
    }

    /**
     * Define the relationship to the Image model.
     * 
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

}