<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    use Blameable;

    /**
     * Define the relationship to the taxonomy.
     * 
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }
}
