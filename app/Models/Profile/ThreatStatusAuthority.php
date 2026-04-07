<?php

namespace App\Models\Profile;

use App\Models\Shared\Reference;
use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ThreatStatusAuthority
 *
 * Represents a threat status authority, which is a reference that describes the
 * authority for a threat status. This model is based on the
 * 'threat_status_authorities' database view, which combines data from the
 * 'references' table and its related extension for threat status authorities.
 *
 * The model includes relationships to the base Reference model and any sidecar
 * data.
 *
 * @property int $id
 * @property int $reference_type_id
 * @property string $author_string
 * @property int|null $year
 * @property string|null $title
 * @property string|null $doi
 * @property string|null $url
 * @property array|null $metadata
 *
 * @property-read Reference $reference
 */
#[Table(
    name: 'threat_status_authorities', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'reference_type_id',
    'author_string',
    'year',
    'title',
    'doi',
    'url',
    'metadata',
])]
class ThreatStatusAuthority extends Model
{
    use HasSidecar;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the reference that this threat status authority belongs to.
     * 
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'reference_id', 'id');
    }

    public function getBaseTable(): string
    {
        return 'references';
    }

    public function getBaseModelClass(): string
    {
        return Reference::class;
    }
    
    public function getExtensionTable(): string
    {
        return 'threat_status_authorities_ext';
    }

    public function getSidecarFields(): array
    {
        return [];
    }
}