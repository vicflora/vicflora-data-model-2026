<?php

namespace App\Models\Mapper;

use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Clickbar\Magellan\Data\Geometries\Point;

/**
 * Class Occurrence
 *
 * Represents a single occurrence record, which captures the details of a specific 
 * observation or collection event. This model is based on the 'mapper.occurrences' 
 * database table, which contains fields for various aspects of the occurrence, such 
 * as the basis of record, location, date, and other relevant information.
 *
 * The model includes relationships to the ParsedName for taxonomic information and 
 * any Assertions made about this occurrence.
 * 
 * @property string $id
 * @property string $basis_of_record
 * @property string $data_resource_uid
 * @property string|null $collection
 * @property string|null $catalog_number
 * @property string|null $scientific_name
 * @property string|null $recorded_by
 * @property string|null $record_number
 * @property \Illuminate\Support\Carbon|null $event_date
 * @property string|null $country
 * @property string|null $state_province
 * @property string|null $locality
 * @property string|null $verbatim_locality
 * @property float|null $decimal_latitude
 * @property float|null $decimal_longitude
 * @property string|null $ibra7_region
 * @property string|null $ibra7_subregion
 * @property string|null $lga2023
 * @property string|null $capad2022
 * @property string|null $bioregion
 * @property string|null $park_res
 * @property string|null $rap
 * @property string|null $establishment_means
 * @property string|null $degree_of_establishment
 * @property bool|null $flowers
 * @property bool|null $fruit
 * @property bool|null $buds
 * @property \Clickbar\Magellan\Data\Geometries\Point|null $geom
 * @property int|null $parsed_name_id
 * @property string|null $data_source
 * @property \Illuminate\Support\Carbon|null $modified
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 *
 * @property-read ParsedName|null $parsedName
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
 */
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
    use HasUuids, Blameable, IncrementsVersion;

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