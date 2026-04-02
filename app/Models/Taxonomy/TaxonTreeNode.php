<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(
    name: 'taxon_tree_nodes', 
    key: 'taxon_concept_id', 
    incrementing: false
)]
#[Fillable([
    'taxon_concept_id',
    'taxon_tree_id',
    'taxon_tree_def_item_id',
    'parent_id',
    'path',
])]
class TaxonTreeNode extends Model
{
    public function taxonTree()
    {
        return $this->belongsTo(TaxonTree::class);
    }


    public function taxonTreeDefItem()
    {
        return $this->belongsTo(TaxonTreeDefItem::class);
    }


    public function parent()
    {
        return $this->belongsTo(TaxonTreeNode::class, 'parent_id');
    }
}
