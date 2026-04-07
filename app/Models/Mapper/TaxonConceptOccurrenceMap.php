<?php

namespace App\Models\Mapper;

use App\Models\Taxonomy\TaxonConcept;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Class TaxonConceptOccurrenceMap
 *
 * Represents a mapping between taxonomic concepts and occurrences. This model
 * is based on the 'mapper.taxon_concept_occurrence_map' materialized view,
 * which captures the associations between taxonomic concepts and their
 * occurrences in the dataset.
 *
 * The model includes relationships to the taxonomic concept (TaxonConcept) and
 * the occurrence (Occurrence).
 *
 * @property-read string $taxon_tree_id
 * @property-read string $taxon_concept_id
 * @property-read string $occurrence_id
 *
 * @property-read TaxonConcept|null $taxonConcept
 * @property-read Occurrence|null $occurrence
 */
#[Table(
    name: 'mapper.taxon_concept_occurrence_map', 
    key: 'id', 
    incrementing: false
)]
#[WithoutTimestamps]
class TaxonConceptOccurrenceMap extends Model
{

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