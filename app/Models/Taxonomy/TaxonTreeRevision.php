<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use App\Observers\TaxonTreeRevisionObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class TaxonTreeRevision
 *
 * Represents a revision of a taxonomic tree, which captures changes made to the
 * structure of a taxonomic tree over time. This model is based on the
 * 'taxon_tree_revisions' database table, which records the history of changes
 * to taxonomic trees.
 *
 * The model includes relationships to the taxon tree being revised, the "from"
 * and "to" nodes that represent the change, the type of change, and the
 * taxonomy version (reference) associated with the revision.
 *
 * @property int $id
 * @property int $taxon_tree_id
 * @property int|null $from_node_id
 * @property int|null $to_node_id
 * @property int|null $change_type_id
 * @property int|null $taxonomy_version_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read TaxonTree $taxonTree
 * @property-read TaxonTreeNode|null $fromNode
 * @property-read TaxonTreeNode|null $toNode
 * @property-read ControlledTerm|null $changeType
 * @property-read TaxonomyVersion|null $taxonomyVersion
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(
    name: 'taxon_tree_revisions', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'taxon_tree_id',
    'version',
    'created_by_id',
    'updated_by_id',
])]
#[ObservedBy(TaxonTreeRevisionObserver::class)]
class TaxonTreeRevision extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * Get the taxon tree that owns the revision.
     * 
     * @return BelongsTo
     */
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class);
    }


    /**
     * Get the "from" node for the revision.
     *
     * @return BelongsTo
     */
    public function fromNode(): BelongsTo
    {
        return $this->belongsTo(TaxonTreeNode::class, 'from_node_id');
    }

    /**
     * Get the "to" node for the revision.
     *
     * @return BelongsTo
     */
    public function toNode(): BelongsTo
    {
        return $this->belongsTo(TaxonTreeNode::class, 'to_node_id');
    }

    /**
     * Get the change type for the revision.
     *
     * @return BelongsTo
     */
    public function changeType(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'change_type_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'CHANGE_TYPE');
            });
    }


    /**
     * Get the taxonomy version (reference) associated with the revision.
     *
     * @return BelongsTo
     */
    public function taxonomyVersion(): BelongsTo
    {
        return $this->belongsTo(TaxonomyVersion::class, 'taxonomy_version_id');
    }
}
