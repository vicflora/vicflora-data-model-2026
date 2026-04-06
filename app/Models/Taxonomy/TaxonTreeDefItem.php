<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaxonTreeDefItem
 *
 * Represents an item in the definition of a taxonomic tree, which specifies the 
 * ranks and their order within a taxonomic tree. This model is based on the 
 * 'taxon_tree_def_items' database table, which captures the structure of taxonomic 
 * trees.
 *
 * The model includes relationships to the TaxonTree to which the item belongs and 
 * the rank (ControlledTerm) that defines the taxonomic rank of the item.
 * 
 * @property int $id
 * @property int $taxon_tree_id
 * @property int|null $rank_id
 * @property string $name
 * @property int $rank_order
 * @property bool $is_required
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read TaxonTree $taxonTree
 * @property-read ControlledTerm|null $rank
 * @property-read \App\Models\Shared\Agent $createdBy
 * @property-read \App\Models\Shared\Agent $updatedBy
 */
#[Table(
    name: 'taxon_tree_def_items', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'taxon_tree_id',
    'rank_id',
    'name',
    'rank_order',
    'is_required',
    'created_by_id',
    'updated_by_id',
])]
class TaxonTreeDefItem extends Model
{
    use Blameable;

    /**
     * Define the relationship to the TaxonTree.
     * 
     * @return BelongsTo
     */
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class);
    }

    /**
     * Define the relationship to the rank (ControlledTerm).
     * We filter the related ControlledTerm to only those in the 'TAXON_RANK' vocabulary.
     * 
     * @return BelongsTo
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'rank_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'TAXON_RANK');
            });
    }
}
