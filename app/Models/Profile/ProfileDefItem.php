<?php

namespace App\Models\Profile;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonTree;
use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class ProfileDefItem
 *
 * Represents an item in the definition of a profile, which specifies the sections 
 * and their order within a profile. This model is based on the 'profile_def_items' 
 * database table, which captures the structure of profiles.
 *
 * The model includes relationships to the TaxonTree to which the item belongs and 
 * the section type (ControlledTerm) that defines the type of section in the profile.
 * 
 * @property int $id
 * @property int $taxon_tree_id
 * @property int|null $profile_section_type_id
 * @property bool $is_required
 * @property int $sort_order
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read TaxonTree $tree
 * @property-read ControlledTerm|null $sectionType
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'profile_def_items')]
#[Fillable([
    'taxon_tree_id',
    'profile_section_type_id', // ControlledTerm: 'Description', 'Biology', etc.
    'is_required',
    'sort_order',
])]
class ProfileDefItem extends Model
{
    use Blameable;

    public function tree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'taxon_tree_id');
    }

    public function sectionType(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'profile_section_type_id');
    }
}