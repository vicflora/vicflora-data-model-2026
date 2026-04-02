<?php

namespace App\Models\Profile;

use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonConcept;
use App\Models\Taxonomy\TreatmentVersion;
use App\Observers\ProfileObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function treatmentVersion(): BelongsTo
    {
        return $this->belongsTo(TreatmentVersion::class, 'treatment_version_id');
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
     * Layer 8c: Images
     * * @return BelongsToMany
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'profile_image_map', 'profile_id', 'image_id')
            ->using(ProfileImageMap::class)
            ->withPivot([
                'taxon_tree_id', 
                'image_role_id', 
                'profile_section_id', 
                'sort_order', 
                'is_published'
            ]);
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