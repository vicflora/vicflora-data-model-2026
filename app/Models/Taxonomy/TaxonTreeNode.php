<?php

namespace App\Models\Taxonomy;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TaxonTreeNode
 *
 * Represents a node in a taxonomic tree, which corresponds to a taxonomic
 * concept (TaxonConcept) and its position within the tree. This model is based
 * on the 'taxon_tree_nodes' database table, which captures the hierarchical
 * structure of taxonomic concepts within a taxonomic tree.
 *
 * The model includes relationships to the TaxonTree to which the node belongs,
 * the TaxonTreeDefItem that defines the rank and name of the node, and the
 * parent node in the tree.
 *
 * @property int $taxon_concept_id
 * @property int $taxon_tree_id
 * @property int|null $taxon_tree_def_item_id
 * @property int|null $parent_id
 * @property string $path
 *
 * @property-read TaxonTree $taxonTree
 * @property-read TaxonTreeDefItem|null $taxonTreeDefItem
 * @property-read TaxonTreeNode|null $parent
 */
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
