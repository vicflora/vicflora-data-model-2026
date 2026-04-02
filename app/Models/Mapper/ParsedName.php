<?php

namespace App\Models\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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