<?php

namespace App\Models\Mapper;

use App\Models\Shared\Agent;
use App\Models\Traits\Auditable;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Occurrence
 *
 * Represents a single occurrence record, which captures the details of a
 * specific observation or collection event. This model is based on the
 * 'mapper.occurrences' database table, which contains fields for various
 * aspects of the occurrence, such as the basis of record, location, date, and
 * other relevant information.
 *
 * The model includes relationships to the ParsedName for taxonomic information
 * and any Assertions made about this occurrence.
 *
 * * Identification & linkages
 * @property string $id (UUID)
 * @property int|null $parsed_name_id
 * @property string $scientific_name
 * @property string $data_source
 * @property string|null $event_date
 * 
 * * Biological status
 * @property string|null $establishment_means
 * @property string|null $degree_of_establishment
 * @property bool $flowers
 * @property bool $fruit
 * @property bool $buds
 * 
 * * Spatial & geography
 * @property mixed $geom (PostGIS Point)
 * @property string|null $lga2023
 * @property string|null $bioregion
 * @property string|null $park_res
 * @property string|null $rap
 * 
 * * Source data
 * @property array{
 *     dataResourceUid: string|null,
 *     collection: string|null,
 *     catalogNumber: string|null,
 *     basisOfRecord: string|null,
 *     recordedBy: string|null,
 *     recordNumber: string|null,
 *     country: string|null,
 *     stateProvince: string|null,
 *     locality: string|null,
 *     verbatimLocality: string|null,
 *     decimalLatitude: float|string|null,
 *     decimalLongitude: float|string|null,
 *     reproductiveCondition: string|null,
 *     establishmentMeans: string|null,
 *     degreeOfEstablishment: string|null,
 *     ibra7Region: string|null,
 *     ibra7Subregion: string|null,
 *     capad2022: string|null,
 * }|null $metadata
 * 
 * * System metadata
 * @property Carbon|null $modified
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * * Relationships
 * @property-read ParsedName|null $parsedName
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
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
    use HasUuids, Auditable;

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