<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Reference;
use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Typification
 *
 * Represents a typification that is separate from the publication, e.g.,
 * lectotypification or conserved type. This model is based on the
 * 'typifications' view, which combines data from the 'references' table with
 * extension data (which is nothing for typifications at the moment).
 *
 * The model includes relationships to the ase Reference model and any sidecar
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
    name: 'typification', 
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
])]
class Typification extends Model
{
    use HasSidecar;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the name of the base table that this model extends.
     *
     * @return string
     */
    public function getBaseTable(): string
    {
        return 'references';
    }

    /**
     * Get the class name of the base model that this model extends.
     *
     * @return string
     */
    public function getBaseModelClass(): string
    {
        return Reference::class;
    }
    
    /**
     * Get the name of the sidecar extension table that holds additional fields for this model.
     *
     * @return string
     */
    public function getExtensionTable(): string
    {
        return 'typifications_ext';
    }

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return [];
    }

}
