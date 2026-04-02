<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\Blameable;
use App\Observers\TaxonTreeRevisionObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    use Blameable;

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
        return $this->belongsTo(TaxonTreeNode::class, 'from_node_id', 'taxon_concept_id');
    }

    /**
     * Get the "to" node for the revision.
     *
     * @return BelongsTo
     */
    public function toNode(): BelongsTo
    {
        return $this->belongsTo(TaxonTreeNode::class, 'to_node_id', 'taxon_concept_id');
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
