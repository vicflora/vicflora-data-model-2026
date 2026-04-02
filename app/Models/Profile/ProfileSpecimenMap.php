<?php

namespace App\Models\Profile;

use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonTree;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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