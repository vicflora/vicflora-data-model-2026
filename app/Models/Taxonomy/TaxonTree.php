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
 * @property string $name
 * @property bool $is_published
 * @property int $taxonomy_id
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
    'created_at',
    'updated_at',
    'guid',
    'name',
    'is_published',
    'taxonomy_id',
    'created_by_id',
    'updated_by_id',
])]
class TaxonTree extends Model
{
    use Auditable;

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
