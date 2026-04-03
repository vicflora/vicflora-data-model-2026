<?php

namespace App\Models\Geography;

use App\Models\Shared\ControlledTerm;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

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
}