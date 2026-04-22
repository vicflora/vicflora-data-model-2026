<?php

namespace App\Models\Shared;

use App\Models\Contracts\Sourceable;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class EntitySourceMap
 *
 * Links a reference to a sourceable entity (e.g., a document, an image, etc.)
 * with optional metadata.
 *
 * @property int $id
 * @property int $reference_id
 * @property string $sourceable_type
 * @property int $sourceable_id
 * @property array|null $metadata
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read Reference $reference
 * @property-read Model|Sourceable $sourceable
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'entity_source_map', key: 'id', incrementing: true)]
#[Fillable([
    'id',
    'reference_id',
    'sourceable_type',
    'sourceable_id',
    'metadata',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class EntitySourceMap extends MorphPivot implements Sourceable
{
    use Auditable;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the reference associated with this source map.
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }

    /**
     * Get the sourceable entity. This will return the related model (e.g.,
     * NomenclaturalType, TaxonConceptMapping) based on the sourceable_type and
     * sourceable_id fields.
     * @return MorphTo
     */
    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }
}