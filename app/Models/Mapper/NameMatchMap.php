<?php

namespace App\Models\Mapper;

use App\Models\Taxonomy\TaxonName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Class NameMatchMap
 *
 * Represents a mapping between parsed names from external sources and the 
 * authoritative taxonomic names in the system. This model is based on the 
 * 'mapper.name_match_map' materialized view, which captures the results of 
 * matching parsed names to taxonomic names.
 *
 * The model includes relationships to the taxonomic name (TaxonName) and the 
 * parsed name (ParsedName).
 * 
 * @property-read int $id
 * @property-read string $taxon_name_id
 * @property-read int $parsed_name_id
 *
 * @property-read TaxonName|null $taxonName
 * @property-read ParsedName|null $parsedName
 */
#[Table(name: 'mapper.name_match_map', primaryKey: 'id', incrementing: false)]
#[WithoutTimestamps]
class NameMatchMap extends Model
{
    protected $casts = [
        'id' => 'integer',
        'taxon_name_id' => 'string', // Points to public.taxon_names.guid
        'parsed_name_id' => 'string', // Points to mapper.parsed_names.id
    ];

    /**
     * Link to the authoritative Botanical Name record.
     */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'taxon_name_id', 'guid');
    }

    /**
     * Link to the raw string/parsed name from the ALA/External source.
     */
    public function parsedName(): BelongsTo
    {
        return $this->belongsTo(ParsedName::class, 'parsed_name_id');
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
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mapper.name_match_map');
    }
}