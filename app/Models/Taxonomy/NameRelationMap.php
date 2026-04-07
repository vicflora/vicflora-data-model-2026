<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\ControlledTerm;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class NameRelationMap
 *
 * Represents a mapping of nomenclatural relationships between taxonomic names.
 * This model is based on the 'name_relations_map' database table, which 
 * captures relationships such as basionym, synonymy, etc., between taxonomic 
 * names.
 *
 * The model includes relationships to the source and target TaxonName, as well 
 * as the type of relationship defined by a ControlledTerm from the 
 * NAME_RELATION_TYPE vocabulary.
 * 
 * @property int $id
 * @property int $from_taxon_name_id
 * @property int $to_taxon_name_id
 * @property int $relation_type_id
 * @property int|null $reference_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read TaxonName $fromTaxonName
 * @property-read TaxonName $toTaxonName
 * @property-read ControlledTerm $relationType
 * @property-read Reference|null $reference
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
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