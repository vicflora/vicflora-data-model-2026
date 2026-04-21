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
 * @property int $taxon_concept_id
 * @property int|null $version_number
 * @property string|null $version_label
 * @property arra|null $data_snapshot
 *
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Reference $reference
 * @property-read Treatment $treatment
 * @property-read Taxonomy $taxonomy
 * @property-read TaxonConcept $taxonConcept
 */
#[Table(
    name: 'treatment_versions_ext', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'treatment_id',
    'taxonomy_id',
    'taxon_concept_id',
    'version_number',
    'version_label',
    'data_snapshot',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class TreatmentVersion extends Model
{
    use IsSidecar;

    protected $casts = [
        'data_snapshot' => 'array',
    ];

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     * This is used by the HasSidecar trait to know which fields to read/write
     * from the sidecar table.
     * @return array
     */
    public function getSidecarFields(): array
    {
        return ['treatment_id'];
    }

    /**
     * Get the reference that this taxonomy version belongs to.
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'id');
    }

    /**
     * Get the treatment that this version belongs to.
     * @return BelongsTo
     */
    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class, 'treatment_id');
    }

    /**
     * Get the taxonomy that this version belongs to.
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'taxonomy_id');    
    }

    /**
     * Get the taxon concept that this version belongs to.
     * @return BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id');
    }
}