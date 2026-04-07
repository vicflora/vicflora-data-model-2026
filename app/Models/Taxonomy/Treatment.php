<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Reference;
use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Treatment
 *
 * Represents a treatment, which is a reference that describes the treatment of
 * a taxonomic concept. This model is based on the 'treatments' database view,
 * which combines data from the 'references' table and its related extension for
 * treatments.
 *
 * The model includes relationships to the base Reference model and any sidecar
 * data, as well as a relationship to the taxonomy version to which the
 * treatment belongs.
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
 * @property-read TaxonomyVersion $taxonomyVersion
 */
#[Table(
    name: 'treatments', 
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
class Treatment extends Model
{
    use HasSidecar;

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
    * Get the reference that this treatment belongs to.
    * 
    * @return BelongsTo
    */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }

    /**
     * Get the taxonomy version that this treatment belongs to.
     * 
     * @return BelongsTo
     */
    public function taxonomyVersion(): BelongsTo
    {
        return $this->belongsTo(TaxonomyVersion::class, 'taxonomy_version_id');
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
     * This is used by the HasSidecar trait to know which table to join to for the sidecar fields.
     * 
     * @return string
     */
    public function getExtensionTable(): string
    {
        return 'treatments_ext';
    }

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     * This is used by the HasSidecar trait to know which fields to read/write from the sidecar table.
     * 
     * @return array
     */
    public function getSidecarFields(): array
    {
        return ['taxonomy_version_id'];
    }
}