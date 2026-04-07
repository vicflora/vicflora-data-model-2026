<?php

namespace App\Models\Mapper;

use App\Models\Shared\Agent;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Clickbar\Magellan\Data\Geometries\MultiPolygon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Carbon;

/**
 * Class MapOverlay
 *
 * Represents a map overlay, which is a polygon that defines a specific area on
 * a map. This model is based on the 'mapper.map_overlays' database table, which
 * captures the details of each map overlay, including its geometry and
 * associated metadata.
 *
 * The model includes relationships to the TaxonConceptMapOverlayMap records
 * that link this overlay to specific taxonomic concepts and their
 * distributions.
 *
 * @property int $id
 * @property string $layer_type
 * @property int $area_fid
 * @property string $area_code
 * @property string $area_name
 * @property MultiPolygon $geom
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(
    name: 'mapper.map_overlays', 
    primaryKey: 'id',
    incrementing: true
)]
#[Fillable([
    'layer_type', 
    'area_fid', 
    'area_code', 
    'area_name', 
    'geom'
])]
class MapOverlay extends Model
{
    use Blameable, IncrementsVersion;

    protected $casts = [
        'area_fid' => 'integer',
        'geom' => MultiPolygon::class,
    ];
}