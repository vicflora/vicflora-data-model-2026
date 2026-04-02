<?php

namespace App\Models\Image;

use App\Models\Profile\Specimen;
use App\Models\Profile\SpecimenImageMap;
use App\Models\Shared\ControlledTerm;
use App\Observers\ImageObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    // Inside App\Models\Image\Image.php

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