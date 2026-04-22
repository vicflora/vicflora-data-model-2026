<?php

namespace App\Models\Glossary;

use App\Models\Contracts\HasLimitation;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class GlossaryLimitationMap 
 * 
 * This model represents the mapping between a glossary
 * term and a limitable entity. It uses a polymorphic relationship to allow
 * flexibility in the types of entities that can be associated with glossary
 * terms.
 * 
 * @property int $id
 * @property int $limitation_id
 * @property string $limitable_type
 * @property int $limitable_id
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read Limitation $limitation
 * @property-read Model|HasLimitation $limitable
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'glossary_limitation_map', key: 'id', incrementing: true)]
#[Fillable([
    'id',
    'limitation_id',
    'limitable_type',
    'limitable_id',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class GlossaryLimitationMap extends MorphPivot implements HasLimitation
{
    use Auditable;

    /**
     * Get the limitation associated with this map.
     * @return BelongsTo
     */    
    public function limitation()
    {        
        return $this->belongsTo(Limitation::class);
    }       

    /**
     * Get the limitable entity. This will return the related model based on the
     * limitable_type and limitable_id fields.
     * @return MorphTo
     */
    public function limitable(): MorphTo
    {
        return $this->morphTo();
    }
}