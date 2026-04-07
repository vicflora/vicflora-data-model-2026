<?php

namespace App\Models\Mapper;

use App\Models\Taxonomy\TaxonConcept;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Class TaxonConceptPhenologyMap
 * 
 * @property-read int $id
 * @property-read string $taxon_concept_id
 * @property-read int $month_numerical
 * @property-read string $month
 * @property-read int $total
 * @property-read int $buds
 * @property-read int $flowers
 * @property-read int $fruit
 * 
 * @property-read TaxonConcept $taxonConcept
 */
#[Table(
    name: 'mapper.taxon_concept_phenology_map', 
    key: 'id', 
    incrementing: false
)]
#[WithoutTimestamps]
class TaxonConceptPhenologyMap extends Model
{
    protected $casts = [
        'id' => 'integer',
        'taxon_concept_id' => 'string',
        'month_numerical' => 'integer',
        'total' => 'integer',
        'buds' => 'integer',
        'flowers' => 'integer',
        'fruit' => 'integer',
    ];
    
    /**
     * Link back to the main Taxon record in the view.
     */
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
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mapper.taxon_concept_phenology_map');
    }
}