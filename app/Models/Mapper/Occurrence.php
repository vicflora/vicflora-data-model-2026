<?php

namespace App\Models\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\Mapper\OccurrenceObserver;
use Clickbar\Magellan\Data\Geometries\Point;

#[Table(
    name: 'mapper.occurrences', 
    primaryKey: 'id', 
    keyType: 'string', 
    incrementing: false
)]
#[Fillable([
    'basis_of_record', 'data_resource_uid', 'collection', 'catalog_number',
    'scientific_name', 'recorded_by', 'record_number', 'event_date',
    'country', 'state_province', 'locality', 'verbatim_locality',
    'decimal_latitude', 'decimal_longitude', 'ibra7_region', 'ibra7_subregion',
    'lga2023', 'capad2022', 'bioregion', 'park_res', 'rap',
    'establishment_means', 'degree_of_establishment', 'flowers', 'fruit', 'buds',
    'geom', 'parsed_name_id', 'data_source', 'modified'
])]
class Occurrence extends Model
{
    use HasUuids;

    protected $casts = [
        'flowers' => 'boolean',
        'fruit' => 'boolean',
        'buds' => 'boolean',
        'modified' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'decimal_latitude' => 'double',
        'decimal_longitude' => 'double',
        'geom' => Point::class,
    ];

    /**
     * Link to the normalized string parser results
     */
    public function parsedName(): BelongsTo
    {
        return $this->belongsTo(ParsedName::class, 'parsed_name_id');
    }
}