<?php

namespace App\Models\Geography;

use App\Models\Shared\Reference;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'area_codes', incrementing: true)]
#[Fillable([
    'area_id', 
    'gazetteer_id', 
    'parent_id', 
    'scheme', 
    'level', 
    'code', 
    'is_primary', 
    'path'
])]
class AreaCode extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * The physical Area entity.
     * 
     * @return BelongsTo
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * The Parent Code in the same scheme (e.g., L2 parent of an L3 code).
     * 
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(AreaCode::class, 'parent_id');
    }

    /**
     * The Child Codes in the same scheme.
     * 
     * @return BelongsTo
     */
    public function children(): HasMany
    {
        return $this->hasMany(AreaCode::class, 'parent_id');
    }

    /**
     * The Reference/Gazetteer that defines this code.
     * 
     * @return BelongsTo
     */
    public function gazetteer(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'gazetteer_id');
    }
}