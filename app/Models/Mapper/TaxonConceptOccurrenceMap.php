<?php

namespace App\Models\Mapper;

use App\Models\Taxonomy\TaxonConcept;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[Table(
    name: 'mapper.taxon_concept_occurrence_map', 
    primary_key: ['taxon_tree_id', 'taxon_concept_id', 'occurrence_id'], 
    incrementing: false
)]
class TaxonConceptOccurrenceMap extends Model
{
    public $timestamps = false;
    protected $keyType = 'string';

    public function occurrence(): BelongsTo
    {
        return $this->belongsTo(Occurrence::class, 'occurrence_id');
    }

    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id', 'guid');
    }

    /**
     * Prevent accidental writes to the Materialized View.
     */
    public function save(array $options = []): bool
    {
        throw new \Exception("Cannot write to a Materialized View.");
    }

    /**
     * Refresh the phenology data.
     */
    public static function refreshView(): void
    {
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mapper.taxon_concept_occurrence_map');
    }

}