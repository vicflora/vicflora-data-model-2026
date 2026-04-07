<?php

namespace App\Models\Image;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class ImageAccessPoint
 *
 * Represents an access point for an image, which provides information about
 * different variants of the image (e.g., thumbnail, preview) and their
 * associated metadata such as format, dimensions, and file size. This model is
 * based on the 'image_access_points' database table, which captures the details
 * of how images can be accessed in different formats and sizes.
 *
 * The model includes relationships to the parent Image and the variant type
 * (ControlledTerm).
 *
 * @property int $id
 * @property int $image_id
 * @property int|null $variant_id
 * @property string $access_uri
 * @property string|null $format
 * @property int|null $width
 * @property int|null $height
 * @property int|null $file_size
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Image $image
 * @property-read ControlledTerm|null $variant
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'image_access_points', key: 'id', incrementing: true)]
#[Fillable([
    'image_id',
    'variant_id',
    'access_iri',
    'format',
    'width',
    'height',
    'file_size'
])]
class ImageAccessPoint extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * The parent Image metadata record.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * The variant type (THUMBNAIL, PREVIEW, etc.)
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'variant_id');
    }
}