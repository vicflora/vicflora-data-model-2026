<?php

namespace App\Models\Geography;

use App\Models\Shared\Reference;
use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Gazetteer
 *
 * Represents a gazetteer, which is a reference that describes a geographical
 * location. This model is based on the 'gazetteers' database view, which
 * combines data from the 'references' table and its related extension for
 * gazetteers.
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
 * @property string|null $code
 *
 * @property-read Reference $reference
 */
#[Table(
    name: 'gazetteers', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'reference_type_id',
    'author_string',
    'year',
    'title',
    'doi',
    'url',
    'metadata',
])]
class Gazetteer extends Model
{
    use HasSidecar;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the reference that this gazetteer belongs to.
     * 
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'reference_id', 'id');
    }

    /**
     * Get the class name of the base model that this model extends.
     * This is used by the HasSidecar trait to know which model to use for the base data.
     * @return string
     */
    public function getBaseModelClass(): string
    {
        return Reference::class;
    }
    
    /**
     * Get the name of the table that the base model is based on.
     * This is used by the HasSidecar trait to know which table to join to for the base fields.
     *
     * @return string
     */
    public function getBaseTable(): string
    {
        return 'references';
    }

    /**
     * Get the name of the extension table that contains the sidecar fields for this model.
     * This is used by the HasSidecar trait to know which table to join to for the sidecar fields.
     * @return string
     */
    public function getExtensionTable(): string
    {
        return 'gazetteers_ext';
    }

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     * This is used by the HasSidecar trait to know which fields to read/write from the sidecar table.
     * @return array
     */
    public function getSidecarFields(): array
    {
        return [];
    }
}