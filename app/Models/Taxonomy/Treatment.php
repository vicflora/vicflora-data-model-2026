<?php

namespace App\Models\Taxonomy;

use App\Models\Profile\Profile;
use App\Models\Shared\Reference;
use App\Models\Traits\IsSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

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
 *
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Reference $reference
 * @property-read Taxonomy $taxonomy
 * @property-read TaxonConcept $taxonConcept
 * @property-read Profile|null $profile
 * @property-read Collection<int, ScientificNameUsageMap> $scientificNameUsages
 * @property-read Collection<int, VernacularNameUsageMap> $vernacularNameUsages
 */
#[Table(
    name: 'treatments_ext', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'taxonomy_id',
    'taxon_concept_id',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class Treatment extends Model
{
    use IsSidecar;

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     * This is used by the HasSidecar trait to know which fields to read/write from the sidecar table.
     * 
     * @return array
     */
    public function getSidecarFields(): array
    {
        return ['taxonomy_id', 'taxon_concept_id'];
    }

    /**
    * Get the reference that this treatment belongs to.
    * 
    * @return BelongsTo
    */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'id');
    }

    /**
     * Get the taxonomy version that this treatment belongs to.
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'taxonomy_id');
    }

    /**
     * Link back to the semantic identity.
     * @return BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id');
    }

    /**
     * The Profile (Description, Biology, etc.) 
     * Since Profile.id matches TaxonConcept.id, we join directly via our extension field.
     * @return HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'taxon_concept_id');
    }

    /**
     * The Nomenclature section (header (accepted name) and citation list (synonyms)) and vernacular names.
     * @return HasMany
     */
    public function scientificNameUsages(): HasMany
    {
        return $this->hasMany(ScientificNameUsageMap::class, 'taxon_concept_id', 'taxon_concept_id');
    }

    /**
     * The Vernacular names applied to the taxon concept in this treatment.
     * @return HasMany
     */
    public function vernacularNameUsages(): HasMany
    {
        return $this->hasMany(VernacularNameUsageMap::class, 'taxon_concept_id', 'taxon_concept_id');
    }
}