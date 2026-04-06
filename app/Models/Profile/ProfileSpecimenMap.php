<?php

namespace App\Models\Profile;

use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonTree;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProfileSpecimenMap
 *
 * Represents the mapping of specimens to profiles, which captures the association of 
 * specimens (vouchers) with specific profiles. This model is based on the 'profile_specimen_map' 
 * database table, which records the specimens linked to profiles along with metadata 
 * about the association.
 *
 * The model includes relationships to the profile, specimen, taxon tree (as a 
 * namespace), and controlled terms for voucher types.
 * 
 * @property int $id
 * @property int $profile_id
 * @property int $specimen_id
 * @property int|null $taxon_tree_id
 * @property int|null $voucher_type_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read Profile $profile
 * @property-read Specimen $specimen
 * @property-read TaxonTree|null $tree
 * @property-read ControlledTerm|null $type
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
 */
class ProfileSpecimenMap extends Pivot
{
    /**
     * The table associated with the pivot model.
     */
    protected $table = 'profile_specimen_map';

    /**
     * Increment the version of the parent Profile whenever a voucher changes.
     */
    protected $touches = ['profile'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'profile_id',
        'specimen_id',
        'taxon_tree_id',
        'voucher_type_id',
        'created_by_id',
        'updated_by_id',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'taxon_concept_id');
    }

    public function specimen(): BelongsTo
    {
        return $this->belongsTo(Specimen::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'voucher_type_id');
    }

    public function tree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'taxon_tree_id');
    }
}