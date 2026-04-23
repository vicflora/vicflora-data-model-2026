<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class TaxonTree
 *
 * Represents a taxonomic tree, which is a hierarchical structure of taxonomic 
 * concepts. This model is based on the 'taxon_trees' database table, which 
 * captures the organization of taxonomic concepts into trees.
 *
 * The model includes a relationship to the taxonomy to which the tree belongs.
 * 
 * @property int $id
 * @property string $guid
 * @property int|null $parent_id
 * @property int|null $root_node_id
 * @property int $taxonomy_id
 * @property string $name
 * @property bool $is_published
 * @property array|null $metadata
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read Taxonomy $taxonomy
 * @property-read Collection<int, TaxonTreeNode> $nodes
 * @property-read Collection<int, TaxonTreeNode> $currentNodes
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
#[Table(
    name: 'taxon_trees', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'guid',
    'parent_id',
    'root_node_id',
    'taxonomy_id',
    'created_at',
    'updated_at',
    'guid',
    'name',
    'is_published',
    'metadata',
    'created_by_id',
    'updated_by_id',
])]
class TaxonTree extends Model
{
    use Auditable;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Define the self-referential relationship to the parent tree.
     * This allows for nested trees, where a tree can have a parent tree.
     * 
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'parent_id');
    }

    /**
     * Define the relationship to the root node of the tree.
     * This is a one-to-one relationship, as a tree can only have one root node.
     * 
     * @return BelongsTo
     */
    public function rootNode(): BelongsTo
    {
        return $this->belongsTo(TaxonTreeNode::class, 'root_node_id');
    }

    /**
     * Define the relationship to the taxonomy.
     * 
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    /**
     * All nodes that have ever existed in this tree.
     * Useful for historical snapshots and audit trails.
     */
    public function nodes(): HasMany
    {
        return $this->hasMany(TaxonTreeNode::class);
    }

    /**
     * A helper for the "HEAD" version.
     * Returns only the nodes that are currently active.
     */
    public function currentNodes(): HasMany
    {
        return $this->nodes()->whereNull('end_date');
    }
}
