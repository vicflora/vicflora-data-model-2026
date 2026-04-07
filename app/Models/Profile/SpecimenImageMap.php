<?php

namespace App\Models\Profile;

use App\Models\Image\Image;
use App\Models\Shared\Agent;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * Class SpecimenImageMap
 *
 * Represents the mapping between specimens and images, allowing for the
 * association of multiple images with a single specimen and vice versa. This
 * model is based on the 'specimen_image_map' database table, which captures the
 * many-to-many relationship between specimens and images.
 *
 * The model includes fields for the specimen ID, image ID, an optional external
 * ID for linking to external systems, and a sort order for ordering images
 * associated with a specimen.
 *
 * @property int $id
 * @property int $specimen_id
 * @property int $image_id
 * @property string|null $external_id
 * @property int|null $sort_order
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read Specimen|null $specimen
 * @property-read Image|null $image
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
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