<?php

namespace App\Models\Profile;

use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonTree;
use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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