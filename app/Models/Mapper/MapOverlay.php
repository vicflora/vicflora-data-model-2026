<?php

namespace App\Models\Mapper;

use Clickbar\Magellan\Data\Geometries\MultiPolygon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public $timestamps = true;

    protected $casts = [
        'area_fid' => 'integer',
        'geom' => MultiPolygon::class,
    ];

    /**
     * The species distributions associated with this specific polygon.
     */
    public function conceptMaps(): HasMany
    {
        return $this->hasMany(TaxonConceptMapOverlayMap::class, 'area_id');
    }
}