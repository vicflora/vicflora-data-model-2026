<?php

namespace App\Models\Profile;

use App\Models\Media\Image;
use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\Reference;
use App\Models\Taxonomy\TaxonConcept;
use App\Models\Taxonomy\TreatmentVersion;
use App\Models\Traits\Auditable;
use App\Models\Traits\HasImages;
use App\Models\Traits\Sourceable;
use App\Observers\ProfileObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Profile
 *
 * Represents a profile for a taxon concept, which is a detailed description of
 * a taxon concept that may include various sections (e.g., descriptions,
 * habitat, distribution) and associated media (e.g., images, specimens). This
 * model is based on the 'profiles' database table, which captures the core
 * information about profiles.
 *
 * The model includes relationships to the taxon concept it describes, the
 * treatment version it may be associated with, the sections that make up the
 * profile, and the specimens and images linked to the profile.
 *
 * @property int $taxon_concept_id
 * @property bool $is_published
 * @property string|null $content
 * @property int|null $treatment_version_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read TaxonConcept $taxonConcept
 * @property-read TreatmentVersion|null $treatmentVersion
 * @property-read Collection|ProfileSection[] $sections
 * @property-read Collection|Specimen[] $specimens
 * @property-read Collection|Image[] $images
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(
    name: 'profiles', 
    key: 'taxon_concept_id', 
    incrementing: false
)]
#[Fillable([
    'taxon_concept_id',
    'is_published',
    'content',
])]
#[ObservedBy(ProfileObserver::class)]
class Profile extends Model
{
    use Auditable, HasImages, Sourceable;
    
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id');
    }

    /**
     * Layer 5: Narrative (Descriptions, Habitat, etc.)
     */
    public function sections(): HasMany
    {
        return $this->hasMany(ProfileSection::class, 'profile_id');
    }

    /**
     * Optional relationship to the TreatmentVersion for this Profile
     *
     * @return BelongsTo
     */
    public function treatmentVersions(): HasMany
    {
        return $this->hasMany(TreatmentVersion::class, 'taxon_concept_id');
    }
    
    /**
     * Layer 6: Specimen Vouchers
     * 
     * @return BelongsToMany
     */
    public function specimens(): BelongsToMany
    {
        return $this->belongsToMany(Specimen::class, 'profile_specimen_map', 'profile_id', 'specimen_id')
            ->using(ProfileSpecimenMap::class)
            ->withPivot(['taxon_tree_id', 'voucher_type_id', 'created_by_id', 'updated_by_id'])
            ->withTimestamps();
    }

    /**
    * Convenience method to get only the cited specimens for this profile.
    * This filters the specimens relationship to only those where the pivot voucher_type_id is 'CITED'.
    * 
    * @return BelongsToMany
    */
    public function citedSpecimens(): BelongsToMany
    {
        return $this->specimens()->wherePivot(
            'voucher_type_id', 
            ControlledTerm::getIdByCode('VOUCHER_TYPE', 'CITED')
        );
    }

    /**
    * Convenience method to get only the representative specimens for this profile.
    * This filters the specimens relationship to only those where the pivot voucher_type_id is 'REPRESENTATIVE'.
     *
     * @return BelongsToMany
     */
    public function representativeSpecimens(): BelongsToMany
    {
        return $this->specimens()->wherePivot(
            'voucher_type_id', 
            ControlledTerm::getIdByCode('VOUCHER_TYPE', 'REPRESENTATIVE')
        );
    }

    /**
     * Layer 8b: Distribution Records
     * 
     * @return HasMany
     */
    public function distribution(): HasMany
    {
        return $this->hasMany(ProfileAreaMap::class, 'profile_id', 'taxon_concept_id');
    }

    /**
     * Convenience method to get only the hero image for this profile.
     *      * 
     * @return BelongsToMany
     */
    public function heroImage(): BelongsToMany
    {
        // Resolve the HERO role ID from your vocabulary
        $heroRoleId = ControlledTerm::getIdByCode('IMAGE_ROLE', 'HERO');

        return $this->images()->wherePivot('image_role_id', $heroRoleId);
    }
}