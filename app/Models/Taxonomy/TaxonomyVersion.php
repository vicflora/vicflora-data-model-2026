<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Reference;
use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaxonomyVersion
 *
 * Represents a version of a taxonomy, which is a reference that describes a taxonomic classification. 
 * This model is based on the 'taxonomy_versions' database view, which combines data from the 
 * 'references' table and its related extension for taxonomy versions.
 *
 * The model includes relationships to the base Reference model and any sidecar data.
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
 * @property-read Taxonomy $taxonomy
 */
#[Table(
    name: 'taxonomy_versions',
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
class TaxonomyVersion extends Model
{
    use HasSidecar;

    /**
     * Get the reference that this taxonomy version belongs to.
     * 
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }

    /**
     * Get the taxonomy that this version belongs to.
     * 
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'taxonomy_id');
    }

    /**
     * Get the class name of the base model that this model extends.
     * This is used by the HasSidecar trait to know which model to use for the base data.
     *
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
     * Get the name of the sidecar extension table that holds additional fields for this model.
     *
     * @return string
     */
    public function getExtensionTable(): string
    {
        return 'taxonomy_versions_ext';
    }

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return ['taxonomy_id'];
    }
}