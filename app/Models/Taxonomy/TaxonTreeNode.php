<?php

namespace App\Models\Taxonomy;

use App\Observers\Taxonomy\TaxonTreeNodeObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 * @property int $id
 * @property int $taxon_tree_id
 * @property int $taxon_concept_id
 * @property int|null $taxon_tree_def_item_id
 * @property int|null $parent_id
 * @property string $path
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * 
 * @property-read TaxonTree $taxonTree
 * @property-read TaxonConcept $taxonConcept
 * @property-read TaxonTreeNode|null $parent
 * @property-read Collection<int, TaxonTreeNode> $replaces
 * @property-read Collection<int, TaxonTreeNode> $replacedBy
 */
#[Table(
    name: 'taxon_tree_nodes', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'taxon_tree_id',
    'taxon_concept_id',
    'taxon_tree_def_item_id',
    'parent_id',
    'path',
    'sort_order',
    'start_date',
    'end_date'
])]
#[ObservedBy(TaxonTreeNodeObserver::class)]
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

    /**
     * Nodes this record replaced (Previous placements)
     */
    public function replaces(): BelongsToMany
    {
        return $this->belongsToMany(
            TaxonTreeNode::class,
            'taxon_tree_revisions',
            'to_node_id', // Local pivot key (us)
            'from_node_id'  // Related pivot key (them)
        )->withPivot('version_id', 'change_type_id', 'effective_date');
    }

    /**
     * Nodes that replaced this record (Subsequent placements)
     */
    public function replacedBy(): BelongsToMany
    {
        return $this->belongsToMany(
            TaxonTreeNode::class,
            'taxon_tree_revisions',
            'from_node_id', // Local pivot key (us)
            'to_node_id'    // Related pivot key (them)
        )->withPivot('version_id', 'change_type_id', 'effective_date');
    }
}
