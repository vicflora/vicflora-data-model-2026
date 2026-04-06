<?php

namespace App\Models\Mapper;

use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property string $scientific_name
 * @property string|null $type
 * @property bool $authors_parsed
 * @property string|null $genus_or_above
 * @property string|null $infrageneric
 * @property string|null $specific_epithet
 * @property string|null $infraspecific_epithet
 * @property string|null $cultivar_epithet
 * @property string|null $strain
 * @property string|null $notho
 * @property string|null $rank_marker
 * @property string|null $authorship
 * @property string|null $bracket_authorship
 * @property string|null $year
 * @property string|null $bracket_year
 * @property string|null $sensu
 * @property bool $parsed
 * @property bool $parsed_partially
 * @property string|null $key
 * @property string|null $nom_status
 * @property string|null $canonical_name
 * @property string|null $canonical_name_with_marker
 * @property string|null $canonical_name_complete
 * @property string|null $remarks
 * @property int|null $vicflora_scientific_name_id
 * @property string|null $name_match_type
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Occurrence> $occurrences
 * @property-read \Illuminate\Database\Eloquent\Collection<int, NameMatchMap> $nameMatches
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
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
    use Blameable, IncrementsVersion;

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