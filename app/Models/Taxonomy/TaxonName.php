<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\ExternalIdentity;
use App\Models\Traits\Auditable;
use App\Models\Traits\HasExternalIdentities;
use App\Models\Traits\HasUsages;
use App\Traits\ManagesSidecars;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class TaxonName
 *
 * Represents a taxonomic name, which can be either a scientific name or a 
 * vernacular name.
 * This model is based on the 'taxon_names_view' database view, which combines 
 * data from the 'taxon_names' table and its related extensions for scientific 
 * and vernacular names.
 *
 * The model includes relationships to the rank (ControlledTerm) and external
 * identities.
 * 
 * @property int $id
 * @property string $guid
 * @property string $name_type
 * @property string $name_string
 * @property int|null $rank_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ScientificName|null $scientificName
 * @property VernacularName|null $vernacularName
 * @property TaxonConceptLabel|null $taxonConceptLabel
 * 
 * @property-read ControlledTerm|null $rank
 * @property-read Collection<int, ExternalIdentity>|null $externalIdentities
 * @property-read Collection<int, TaxonNameUsageMap> $usages
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 * 
 */
#[Table(
    name: 'taxon_names', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'guid',
    'name_type',
    'name_string',
    'rank_id',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class TaxonName extends Model
{
    use Auditable, ManagesSidecars, HasExternalIdentities;

    /**
     * Set default attributes
     *
     * @var array
     */
    protected $attributes = [
        'name_type' => 'SCIENTIFIC_NAME',
    ];

    /**
     * Set base table for sidecars
     * This property is used by the ManageSidecars trait
     *
     * @return string
     */
    protected function baseTable(): string
    {
        return 'taxon_names';
    }

    /**
     * Set base table fields for sidecars
     * This property is used by the ManageSidecars trait
     *
     * @return array
     */
    protected function baseTableFields(): array
    {
        return [
            'id', 
            'guid',
            'name_type',
            'name_string', 
            'rank_id'
        ];
    }

    /**
     * Select sidecar model based on the name_type attribute
     * 
     * This property is used by the ManagesSidecar trait
     *
     * @param array $attributes
     * @return void
     */
    public function selectSidecarModel(array $attributes = [])
    {
        $role = $attributes['name_type'] ?? $this->name_type ?? 'SCIENTIFIC_NAME';

        return match($role) {
            'SCIENTIFIC_NAME' => ScientificName::findOrNew($this->id),
            'VERNACULAR_NAME' => VernacularName::findOrNew($this->id),
            'TAXON_CONCEPT_LABEL' => TaxonConceptLabel::findOrNew($this->id),
            default => null,
        };
    }

    /*
     * Sidecar relationships
     */

    /**
     * Scientific Name sidecar
     *
     * @return HasOne
     */
    public function scientificName(): HasOne
    {
        return $this->hasOne(ScientificName::class, 'id');
    }

    /**
     * Vernacular Name sidecar
     *
     * @return HasOne
     */
    public function vernacularName(): HasOne
    {
        return $this->hasOne(VernacularName::class, 'id');
    }

    /**
     * Taxon Concept Label sidecar
     *
     * @return HasOne
     */
    public function taxonConceptLabel(): HasOne
    {
        return $this->hasOne(TaxonConceptLabel::class, 'id');
    }

    /**
     * Define the relationship to the rank (ControlledTerm).
     * We filter the related ControlledTerm to only those in the 'TAXON_RANK' vocabulary.
     * 
     * @return BelongsTo
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'rank_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'TAXON_RANK');
            });
    }

    /**
     * Get all instances where this name (in any of its roles) 
     * has been cited or used in literature.
     * 
     * @return HasMany
     */
    public function usages(): HasMany
    {
        // Since all your name view-models share the base 'id' 
        // from the taxon_names table, this relationship remains consistent.
        return $this->hasMany(TaxonNameUsageMap::class, 'taxon_name_id');
    }

}
