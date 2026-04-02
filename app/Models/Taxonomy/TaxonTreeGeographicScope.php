<?php

namespace App\Models\Taxonomy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'public.taxon_tree_geographic_scope_map', primaryKey: 'id')]
#[Fillable(['taxon_tree_id', 'scope'])]
class TaxonTreeGeographicScopeMap extends Model
{
    public $timestamps = false;

    /**
     * The Taxon Tree this geographic scope belongs to.
     * 
     * @return BelongsTo
     */
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class);
    }
}