<?php

namespace App\Models\Geography;

use App\Models\Profile\ThreatStatusAuthority;
use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Area
 *
 * Represents a geographical area, which can be a continent, country, state,
 * etc. This model is based on the 'areas' database table in the 'shared'
 * schema, which captures the hierarchical structure of geographical areas.
 *
 * The model includes relationships to the type of area (ControlledTerm), the
 * parent area (for hierarchical relationships), and any child areas. It also
 * has a relationship to AreaCode for storing various codes associated with the
 * area.
 *
 * @property int $id
 * @property string $name
 * @property int|null $area_type_id
 * @property bool $is_accepted
 * @property int|null $parent_id
 * @property int|null $accepted_id
 * @property int|null $threat_status_authority_id
 * @property string|null $area_path
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read ControlledTerm|null $type
 * @property-read Area|null $parent
 * @property-read Collection<int, Area> $children
 * @property-read Collection<int, AreaCode> $areaCodes
 * @property-read ThreatStatusAuthority|null $threatStatusAuthority
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'areas', schema: 'shared', incrementing: true)]
#[Fillable([
    'name',
    'area_type_id',
    'is_accepted',
    'parent_id',
    'accepted_id',
    'area_path',
    'created_by_id',
    'updated_by_id',
])]
class Area extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * The type of area (e.g., Countinent, Country, State).
     * 
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(ControlledTerm::class, 'area_type_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'AREA_TYPE');
            });
    }

    /**
     * The Parent Area (e.g., a County's parent might be a State).
     * 
     * @return BelongsTo
     */
    public function parent() 
    { 
        return $this->belongsTo(Area::class, 'parent_id'); 
    }
    
    /**
     * The Child Areas (e.g., a State's children might be Counties).
     * 
     * @return HasMany
     */
    public function children() { 
        return $this->hasMany(Area::class, 'parent_id'); 
    }

    /**
    * The AreaCodes associated with this Area.
    * 
    * @return HasMany
    */
    public function areaCodes() 
    { 
        return $this->hasMany(AreaCode::class); 
    }


    /**
     * Threat Status Authority for this jurisdiction
     *
     * @return BelongsTo
     */
    public function threatStatusAuthority(): BelongsTo
    {
        return $this->belongsTo(ThreatStatusAuthority::class, 'threat_status_authority_id');
    }
}