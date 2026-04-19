<?php

namespace App\Models\Media;

use App\Models\Profile\Specimen;
use App\Models\Profile\SpecimenImageMap;
use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use App\Observers\ImageObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Image
 *
 * Represents an image associated with a taxon concept or profile, which may include metadata such as the creator, caption, rights holder, and license information. This model is based on the 'images' database table, which captures the core information about images used in the application.
 *
 * The model includes relationships to the image type (ControlledTerm), license (ControlledTerm), and specimens that the image represents.
 * 
 * @property int $id
 * @property string $uri
 * @property int|null $image_type_id
 * @property string|null $creator
 * @property string|null $caption
 * @property string|null $rights_holder
 * @property int|null $license_id
 * @property array|null $metadata
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read ControlledTerm|null $imageType
 * @property-read ControlledTerm|null $license
 * @property-read Collection<int, Specimen> $specimens
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(
    name: 'images', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'uri',
    'image_type_id',
    'creator',
    'caption',
    'rights_holder',
    'license_id',
    'metadata',
])]
#[ObservedBy(ImageObserver::class)]
class Image extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    public function imageType(): BelongsTo {
        return $this->belongsTo(ControlledTerm::class, 'image_type_id');
    }

    public function license(): BelongsTo {
        return $this->belongsTo(ControlledTerm::class, 'license_id');
    }



    public function accessPoints()
    {
        return $this->hasMany(ImageAccessPoint::class);
    }

    /**
     * Helper to get the Thumbnail URL directly.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $variantId = ControlledTerm::getIdByCode('IMAGE_VARIANT', 'THUMBNAIL');
        
        return $this->accessPoints
            ->where('variant_id', $variantId)
            ->first()
            ?->access_iri;
    }

    // Inside App\Models\Media\Image.php

    /**
     * Get the specimens this image represents.
     */
    public function specimens(): BelongsToMany
    {
        return $this->belongsToMany(Specimen::class, 'specimen_image_map')
            ->using(SpecimenImageMap::class)
            ->withPivot(['external_id', 'sort_order']);
    }

    /**
     * Get the licensing information for this image, with fallbacks based on 
     * the presence of source and license metadata.
     *
     * @return string
     */
    public function getActiveLicenseAttribute(): string
    {
        // 1. Return the explicit license if it exists
        if ($this->license) {
            return $this->license;
        }

        // 2. Fallback to a default if no license and no source (Public Domain or CC-BY)
        if (!$this->source) {
            return "CC BY 4.0"; 
        }

        // 3. If there is a source but no license, it might be "Used with permission"
        return "All Rights Reserved (Source: {$this->source})";
    }
}