<?php

namespace App\Models\Geography;

use App\Models\Shared\Agent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class AreaCode
 *
 * Represents a code for an area, which is a specific identifier for a
 * geographic area. This model is based on the 'area_codes' database table,
 * which captures the various codes that can be associated with an area, such as
 * codes from different gazetteers or classification schemes.
 *
 * The model includes relationships to the Area it belongs to, its parent code
 * (if any), its child codes, and the gazetteer/reference that defines it.
 *
 * @property int $id
 * @property int $area_id
 * @property int|null $gazetteer_id
 * @property int|null $parent_id
 * @property string|null $scheme
 * @property string|null $level
 * @property string|null $code
 * @property bool $is_primary
 * @property string|null $path
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Area $area
 * @property-read AreaCode|null $parent
 * @property-read Collection<int, AreaCode> $children
 * @property-read Gazetteer|null $gazetteer
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
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
    use Auditable;

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
        return $this->belongsTo(Gazetteer::class);
    }
}