<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
