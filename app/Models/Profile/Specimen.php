<?php

namespace App\Models\Profile;

use App\Models\Media\Image;
use App\Models\Shared\Reference;
use App\Models\Traits\Blameable;
use App\Models\Traits\HasImages;
use App\Models\Traits\IncrementsVersion;
use App\Models\Traits\Sourceable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Specimen
 *
 * Represents a specimen, which is a physical example of an organism that is
 * used as a reference for scientific study. This model is based on the
 * 'specimens' database table, which captures the details of specimens used in
 * profiles.
 *
 * The model includes relationships to external sources (Reference) and profiles
 * (Profile) through pivot tables, as well as a relationship to images (Image)
 * that represent herbarium sheets or other visual documentation of the
 * specimen.
 *
 * @property int $id
 * @property string|null $institution_code
 * @property string|null $collection_code
 * @property string|null $catalog_number
 * @property string|null $source_url
 * @property array|null $metadata
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Reference|null $externalSource
 * @property-read Collection|Profile[] $profiles
 * @property-read Collection|Image[] $images
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
    'source_url',
    'external_source_id',
    'metadata',
    'created_by_id',
    'updated_by_id',
])]
class Specimen extends Model
{
    use Blameable, IncrementsVersion, HasImages, Sourceable;

    protected $casts = [
        'metadata' => 'array',
    ];

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
     * Helper to get the primary sheet image.
     */
    public function primaryImage()
    {
        return $this->images()->orderByPivot('sort_order')->first();
    }
}
