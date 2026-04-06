<?php

namespace App\Models\Profile;

use App\Models\Image\Image;
use App\Models\Image\ImageCaption;
use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonTree;
use App\Observers\ProfileImageMapObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProfileImageMap
 *
 * Represents the mapping of images to profiles, which captures the association of 
 * images with specific profiles. This model is based on the 'profile_image_map' 
 * database table, which records the images linked to profiles along with metadata 
 * about the association.
 *
 * The model includes relationships to the profile, image, taxon tree (as a 
 * namespace), and controlled terms for image captions and licenses.
 * 
 * @property int $id
 * @property int $profile_id
 * @property int $image_id
 * @property int|null $taxon_tree_id
 * @property int|null $image_caption_id
 * @property int|null $license_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read Profile $profile
 * @property-read Image $image
 * @property-read TaxonTree|null $taxonTree
 * @property-read ImageCaption|null $imageCaption
 * @property-read ControlledTerm|null $license
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
 */
#[Table(
    name: 'profile_image_map', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'profile_id',
    'taxon_tree_id',
    'image_id',
    'image_role_id',
    'profile_section_id',
    'sort_order',
    'is_published',
    'caption',
    'created_by_id',
    'updated_by_id',
])]
#[ObservedBy(ProfileImageMapObserver::class)]
class ProfileImageMap extends Model
{
    /**
     * Automatically update the Profile version when an image mapping changes.
     */
    protected $touches = ['profile'];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationships
     */

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'taxon_concept_id');
    }

    public function tree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'taxon_tree_id');
    }

    public function image(): BelongsTo
    {
        // Points to your central Media/Image asset table
        return $this->belongsTo(Image::class);
    }

    public function caption()
    {
        return $this->belongsTo(ImageCaption::class, 'image_caption_id');
    }

    public function role(): BelongsTo
    {
        // ControlledTerm for HERO, IMAGE, etc.
        return $this->belongsTo(ControlledTerm::class, 'image_role_id');
    }

    public function section(): BelongsTo
    {
        // Optional link to anchor this image to a specific narrative section
        return $this->belongsTo(ProfileSection::class, 'profile_section_id');
    }

    /**
     * Scope: Only get images that are the designated 'Hero' for the profile.
     */
    public function scopeHero($query)
    {
        return $query->where(
            'image_role_id', 
            ControlledTerm::getIdByCode('IMAGE_ROLE', 'HERO')
        );
    }
}