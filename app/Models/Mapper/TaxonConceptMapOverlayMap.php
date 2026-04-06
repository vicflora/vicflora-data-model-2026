<?php

namespace App\Models\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Taxonomy\TaxonConcept;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Support\Facades\DB;

/**
 * Class TaxonConceptMapOverlayMap
 * 
 * @property-read int  $id
 * @property-read string $taxon_concept_id
 * @property-read string $taxon_tree_id
 * @property-read string $layer
 * @property-read int $map_overlay_id
 * @property-read string $area_name
 * @property-read string $occurrence_status
 * @property-read string $establishment_means
 * @property-read string $degree_of_establishment
 * 
 * @property-read TaxonConcept $taxonConcept
 * @property-read MapOverlay $mapOverlay
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|static layer(string $layer)
 */
#[Table(
    name: 'mapper.taxon_concept_map_overlay_map', 
    primaryKey: 'id', 
    incrementing: false
)]
#[WithoutTimestamps]
class TaxonConceptMapOverlayMap extends Model
{

    /**
     * The Taxon Concept this area distribution belongs to.
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id', 'guid');
    }

    /**
     * The specific Map Overlay (LGA, IBRA, etc.)
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(MapOverlay::class, 'area_id');
    }

    /**
     * Scope to filter by a specific layer (e.g., 'bioregion')
     */
    public function scopeLayer($query, string $layer)
    {
        return $query->where('layer', $layer);
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
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mapper.taxon_concept_map_overlay_map');
    }
}