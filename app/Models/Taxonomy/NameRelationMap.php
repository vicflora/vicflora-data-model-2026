<?php

namespace App\Models\Taxonomy;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(
    name: 'name_relations_map', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'from_taxon_name_id',
    'to_taxon_name_id',
    'relation_type_id',
])]
class NameRelationMap extends Model
{
    /**
     * Get the source TaxonName of the relationship.
     * This is the "from" name in the relationship, e.g., the basionym in a nomenclatural relationship.
     * 
     * @return BelongsTo
     */
    public function fromTaxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'from_taxon_name_id');
    }

    /**
     * Get the target TaxonName of the relationship.
     * This is the "to" name in the relationship, e.g., the currently accepted name in a nomenclatural relationship.
     * 
     * @return BelongsTo
     */
    public function toTaxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'to_taxon_name_id');
    }

    /**
     * Get the type of nomenclatural relationship, 
     * strictly scoped to the NAME_RELATION_TYPE vocabulary.
     * 
     * @return BelongsTo
     */
    public function relationType(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'relation_type_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'NAME_RELATION_TYPE');
            });
    }
}