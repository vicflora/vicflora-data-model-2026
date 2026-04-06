<?php

namespace App\Models\Profile;

use App\Models\Image\Image;
use App\Models\Shared\Reference;
use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Specimen
 *
 * Represents a specimen, which is a physical example of an organism that is used 
 * as a reference for scientific study. This model is based on the 'specimens' 
 * database table, which captures the details of specimens used in profiles.
 *
 * The model includes relationships to external sources (Reference) and profiles 
 * (Profile) through pivot tables, as well as a relationship to images (Image) 
 * that represent herbarium sheets or other visual documentation of the specimen.
 * 
 * @property int $id
 * @property string|null $institution_code
 * @property string|null $collection_code
 * @property string|null $catalog_number
 * @property string|null $recorded_by
 * @property string|null $record_number
 * @property \Illuminate\Support\Carbon|null $event_date
 * @property string|null $country
 * @property string|null $state_province
 * @property string|null $locality
 * @property float|null $decimal_latitude
 * @property float|null $decimal_longitude
 * @property string|null $verbatim_elevation
 * @property string|null $habitat
 * @property string|null $source_url
 * @property int|null $external_source_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read Reference|null $externalSource
 * @property-read \Illuminate\Database\Eloquent\Collection|Profile[] $profiles
 * @property-read \Illuminate\Database\Eloquent\Collection|Image[] $images
 */
#[Table(
    name: 'specimens', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'institution_code',
    'collection_code',
    'catalog_number',
    'recorded_by',
    'record_number',
    'event_date',
    'country',
    'state_province',
    'locality',
    'decimal_latitude',
    'decimal_longitude',
    'verbatim_elevation',
    'habitat',
    'source_url',
    'external_source_id',
    'created_by_id',
    'updated_by_id',
])]
class Specimen extends Model
{
    use Blameable;

    /**
     * Define the relationship to the external source reference.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function externalSource()
    {
        return $this->belongsTo(Reference::class, 'external_source_id');
    }

    /**
     * Define the many-to-many relationship to profiles through the profile_specimen_map pivot table.
     * We include the additional pivot fields 'taxon_tree_id' and 'voucher_type_id'.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_specimen_map', 'specimen_id', 'profile_id')
            ->using(ProfileSpecimenMap::class)
            ->withPivot(['taxon_tree_id', 'voucher_type_id'])
            ->withTimestamps();
    }

    /**
     * Get the images (herbarium sheets) associated with this specimen.
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'specimen_image_map')
            ->using(SpecimenImageMap::class)
            ->withPivot(['external_id', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Helper to get the primary sheet image.
     */
    public function primaryImage()
    {
        return $this->images()->orderByPivot('sort_order')->first();
    }
}
