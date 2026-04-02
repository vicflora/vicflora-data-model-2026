<?php

namespace App\Models\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Taxonomy\TaxonConcept;
use Illuminate\Support\Facades\DB;

#[Table(
    name: 'mapper.taxon_concept_map_overlay_map', 
    primaryKey: 'id', 
    incrementing: false
)]
class TaxonConceptMapOverlayMap extends Model
{
    /**
     * Disable timestamps if you don't need to track 
     * when each individual intersection was created.
     */
    public $timestamps = false;

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