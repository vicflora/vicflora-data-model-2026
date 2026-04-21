<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Reference;
use App\Models\Traits\HasSidecar;
use App\Models\Traits\IsSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaxonomyVersion
 *
 * Represents a version of a taxonomy, which is a reference that describes a
 * taxonomic classification. This model is based on the 'taxonomy_versions'
 * database view, which combines data from the 'references' table and its
 * related extension for taxonomy versions.
 *
 * The model includes relationships to the base Reference model and any sidecar
 * data.
 *
 * @property int $id
 * @property int $taxonomy_id
 *
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Reference $reference
 * @property-read Taxonomy $taxonomy
 */
#[Table(
    name: 'taxonomy_versions_ext',
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'taxonomy_id',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class TaxonomyVersion extends Model
{
    use IsSidecar;

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return ['taxonomy_id'];
    }

    /**
     * Get the reference that this taxonomy version belongs to.
     * 
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'id');
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
}