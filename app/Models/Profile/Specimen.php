<?php

namespace App\Models\Profile;

use App\Models\Shared\Reference;
use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
