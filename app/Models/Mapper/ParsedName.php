<?php

namespace App\Models\Mapper;

use App\Models\Shared\Agent;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illumimate\Support\Carbon;

/**
 * Class ParsedName
 *
 * Represents a parsed scientific name in the mapping system. This model is 
 * based on the 'parsed_names' database table, which captures the details of 
 * each parsed name, including its components and metadata about the parsing 
 * process.
 *
 * The model includes relationships to the Occurrences that used this specific 
 * parsed string and the taxonomic matches across different trees.
 * 
 * @property int $id
 * 
 * * Unprocessed scientific name string from occurrence data
 * @property string $scientific_name
 * 
 * * Type of name (from GBIF Name Parser)
 * @property string|null $type
 * 
 * * GBIF Name Parsing API metadata
 * @property array{
 *     authorsParsed: bool|null,
 *     genusOrAbove: string|null,
 *     infraGeneric: string|null, 
 *     specificEpithet: string|null, 
 *     infraspecificEpithet: string|null, 
 *     cultivarEpithet: string|null, 
 *     strain: string|null, 
 *     notho: string|null, 
 *     rankMarker: string|null, 
 *     authorship: string|null, 
 *     bracketAuthorship: string|null, 
 *     year: string|null, 
 *     bracketYear: string|null, 
 *     sensu: string|null, 
 *     parsed: bool, 
 *     parsedPartially: bool,
 *     key: string|null, 
 *     nomStatus: string|null, 
 *     remarks: string|null, 
 * }|null $metadata
 * 
 * * Canonical names
 * @property string|null $canonical_name
 * @property string|null $canonical_name_with_marker
 * @property string|null $canonical_name_complete
 *
 * * Name matching
 * @property int|null $vicflora_scientific_name_id
 * @property string|null $name_match_type
 * 
 * * System metadata
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * * Relationships
 * @property-read Collection<int, Occurrence> $occurrences
 * @property-read Collection<int, NameMatchMap> $nameMatches
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'parsed_names', primaryKey: 'id')]
#[Fillable([
    'scientific_name', 'type', 'authors_parsed', 'genus_or_above',
    'infrageneric', 'specific_epithet', 'infraspecific_epithet',
    'cultivar_epithet', 'strain', 'notho', 'rank_marker', 'authorship',
    'bracket_authorship', 'year', 'bracket_year', 'sensu', 'parsed',
    'parsed_partially', 'key', 'nom_status', 'canonical_name',
    'canonical_name_with_marker', 'canonical_name_complete', 'remarks',
    'vicflora_scientific_name_id', 'name_match_type'
])]
class ParsedName extends Model
{
    use Auditable;

    protected $casts = [
        'authors_parsed' => 'boolean',
        'parsed' => 'boolean',
        'parsed_partially' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The occurrences that used this specific parsed string.
     */
    public function occurrences(): HasMany
    {
        return $this->hasMany(Occurrence::class, 'parsed_name_id');
    }

    /**
     * The taxonomic matches across different trees.
     */
    public function nameMatches(): HasMany
    {
        return $this->hasMany(NameMatchMap::class, 'parsed_name_id');
    }
}