<?php

namespace App\Models\Search;

use Illuminate\Database\Eloquent\Model;
use App\Models\Taxonomy\TaxonConcept;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[Table(name: 'search_mv', primaryKey: 'id', keyType: 'string', incrementing: false)]
#[WithoutTimestamps]
class Search extends Model
{

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_informal' => 'boolean',
        'mappings' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'rank_order' => 'integer',
    ];

    /**
     * Get the source Taxon Concept for more detailed profile data.
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id', 'guid');
    }

    /**
     * Scope: Only current botanical concepts.
     */
    public function scopeCurrent($query)
    {
        return $query->where('status', 'current');
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
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY public.search_mv');
    }

}