<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\ExternalIdentity;
use App\Models\Shared\Reference;
use App\Models\Traits\Auditable;
use App\Models\Traits\Linkable;
use App\Observers\TaxonConceptObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * Represents a specific taxonomic concept, which is a combination of a 
 * TaxonName and an "according to" Reference. This allows us to capture 
 * different taxonomic opinions about the same name.
 * 
 * @property int $id
 * @property string $guid
 * @property int $taxon_tree_id
 * @property int $taxon_name_id
 * @property int $according_to_id
 * @property int $rank_id
 * @property int $version
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null     $updated_at
 * 
 * @property-read TaxonTree $taxonTree
 * @property-read TaxonName $taxonName
 * @property-read Reference $accordingTo
 * @property-read TaxonConceptLabel $label
 * @property-read string $title
 * @property-read ControlledTerm $rank
 * @property-read ScientificName|null $acceptedName
 * @property-read Collection<int, ScientificName> $synonyms
 * @property-read Collection<int, VernacularName> $vernacularNames
 * @property-read VernacularName|null $preferredVernacularName
 * @property-read Collection<int, TaxonConceptMapping> $mappings
 * @property-read Collection<int, TaxonConcept> $isCongruentWith
 * @property-read Collection<int, TaxonConcept> $includes
 * @property-read Collection<int, TaxonConcept> $isIncludedIn
 * @property-read Collection<int, TaxonConcept> $partiallyOverlaps
 * @property-read Collection<int, TaxonConcept> $isDisjointFrom
 * @property-read Collection<int, TaxonConcept> $intersects
 * @property-read Collection<int, ExternalIdentity> $externalIdentities
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 * 
 */
#[Table(
    name: 'taxon_concepts', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'guid',
    'taxon_name_id',
    'according_to_id',
    'rank_id',
    'created_by_id',
    'updated_by_id',
])]
#[ObservedBy(TaxonConceptObserver::class)]
class TaxonConcept extends Model
{
    use Auditable, Linkable;

    /**
     * The TaxonTree this concept belongs to.
     * @return BelongsTo
     */
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'taxon_tree_id');
    }

    /**
     * The Name being used in this specific concept.
     * @return BelongsTo
     */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'taxon_name_id');
    }

    /**
     * The Reference ("SEC" or "Sensu") providing the context.
     * @return BelongsTo
     */
    public function accordingTo(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'according_to_id');
    }

    /**
     * The specific Concept Label sidecar for this concept.
     * Provides the "Name sec. Author" string.
     * @return HasOne
     */
    public function label(): HasOne
    {
        return $this->hasOne(TaxonConceptLabel::class, 'taxon_concept_id');
    }

    /**
     * Get the concept title (dcterms:title).
     * * Returns the "sec." label if available, otherwise falls back 
     * to the nomenclatural full name.
     * @return Attribute
     */
    protected function title(): Attribute
    {
        return Attribute::get(function (mixed $value, array $attributes) {
            // 1. Check if the label relationship is already loaded
            if ($this->relationLoaded('label') && $this->label) {
                return $this->label->name_string;
            }

            // 2. Fallback to the nomenclatural name
            // Using the relationship ensures we get the formatted name string
            return $this->taxonName?->name_string ?? 'Unknown Taxon';
        });
    }

    /**
     * The Rank of this concept in the current taxonomic opinion.
     * @return BelongsTo
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'rank_id')
            ->whereHas('vocabulary', fn($q) => $q->where('code', 'TAXON_RANK'));
    }

    /**
     * Accepted name for this concept
     * @return HasOneThrough
     */
    public function acceptedName(): HasOneThrough
    {
        $acceptedRoleId = ControlledTerm::getIdByCode('NAME_USAGE_ROLE', 'ACCEPTED');

        return $this->hasOneThrough(
            ScientificName::class,
            ScientificNameUsageMap::class,
            'taxon_concept_id',     // FK on TaxonName pointing to "this" concept
            'id',                   // FK on TaxonName (the target)
            'id',                   // Local key on "this" concept
            'taxon_name_id'         // Local key on TaxonNameUsageMap pointing to the ScientificName
        )
        ->whereHas('taxonName', fn($q) => $q->where('name_type', 'SCIENTIFIC'))
        ->where('name_usage_role_id', $acceptedRoleId);
    }

    /**
     * Synonyms for this taxon concept.
     * @return HasManyThrough
     */
    public function synonyms(): HasManyThrough
    {
        // 1. Resolve the ID once (it's cached, so this is instant after the first call)
        $synonymRoleId = ControlledTerm::getIdByCode('NAME_USAGE_ROLE', 'SYNONYM');

        return $this->hasManyThrough(
            ScientificName::class,
            ScientificNameUsageMap::class,
            'taxon_concept_id',
            'id',
            'id',
            'taxon_name_id'
        )
        ->whereHas('taxonName', fn($q) => $q->where('name_type', 'SCIENTIFIC'))
        ->where('name_usage_role_id', $synonymRoleId);
    }

    /**
     * Vernacular names for this taxon concept.
     * @return HasManyThrough
     */
    public function vernacularNames(): HasManyThrough
    {
        return $this->hasManyThrough(
            VernacularName::class,
            VernacularNameUsageMap::class,
            'taxon_concept_id',     // FK on TaxonName pointing to "this" concept
            'id',                   // FK on TaxonName (the target)
            'id',                   // Local key on "this" concept
            'taxon_name_id'         // Local key on TaxonNameUsageMap pointing to the VernacularName
        )
        ->whereHas('taxonName', fn($q) => $q->where('name_type', 'VERNACULAR'));
    }

    /**
     * Preferred vernacular name for this taxon concept
     * @return HasOneThrough
     */
    public function preferredVernacularName(): HasOneThrough
    {
        return $this->hasOneThrough(
            VernacularName::class,
            VernacularNameUsageMap::class,
            'taxon_concept_id',     // FK on TaxonName pointing to "this" concept
            'id',                   // FK on TaxonName (the target)
            'id',                   // Local key on "this" concept
            'taxon_name_id'         // Local key on TaxonNameUsageMap pointing to the VernacularName
        )
        ->where('is_preferred', true)
        ->whereHas('taxonName', fn($q) => $q->where('name_type', 'VERNACULAR'));
    }

    /**
     * Get the full historical audit trail of where this concept 
     * has been placed across all versions of the taxonomic tree.
     */
    public function taxonomicPlacementHistory(): HasMany
    {
        // Points to the new non-unique taxon_concept_id in taxon_tree_nodes
        return $this->hasMany(TaxonTreeNode::class, 'taxon_concept_id');
    }

    /**
     * Helper to get the single currently active placement.
     */
    public function currentPlacement(): HasOne
    {
        return $this->hasOne(TaxonTreeNode::class, 'taxon_concept_id')
            ->whereNull('end_date');
    }

    /**
     * All mappings where this concept is the subject. Inverse mappings will be 
     * materialized as separate records with the subject and object reversed, so 
     * this relationship captures all mappings originating from this concept.
     * 
     * @return HasMany
     */
    public function mappings(): HasMany
    {
        return $this->hasMany(TaxonConceptMapping::class, 'subject_taxon_concept_id');
    }

    /**
     * RCC-5 Mappings: Congruent (==)
     * 
     * @return HasManyThrough
     */
    public function isCongruentWith(): HasManyThrough
    {
        return $this->createMappingRelationship('IS_CONGRUENT_WITH');
    }

    /**
     * RCC-5 Mappings: Includes (>)
     * 
     * @return HasManyThrough
     */
    public function includes(): HasManyThrough
    {
        return $this->createMappingRelationship('INCLUDES');
    }

    /**
     * RCC-5 Mappings: Is Included In (<)
     * 
     * @return HasManyThrough
     */
    public function isIncludedIn(): HasManyThrough
    {
        return $this->createMappingRelationship('IS_INCLUDED_IN');
    }

    /**
     * RCC-5 Mappings: Overlaps (><)
     * 
     * @return HasManyThrough
     */
    public function partiallyOverlaps(): HasManyThrough
    {
        return $this->createMappingRelationship('PARTIALLY_OVERLAPS');
    }

    /**
     * RCC-5 Mappings: Disjoint (!|)
     * 
     * @return HasManyThrough
     */
    public function isDisjointWith(): HasManyThrough
    {
        return $this->createMappingRelationship('IS_DISJOINT_WITH');
    }

    /**
     * Extra mapping relation in TCS: subject and object intersect if they 
     * have at least one member in common.
     * 
     * @return HasManyThrough
     */
    public function intersects(): HasManyThrough
    {
        return $this->createMappingRelationship('INTERSECTS');
    }

    /**
     * Helper to build the HasManyThrough bridge for RCC-5 Mappings.
     * 
     * @param string $mappingCode The code of the mapping relation (e.g. 'IS_CONGRUENT_WITH').
     * @return HasManyThrough
     */
    protected function createMappingRelationship(string $mappingCode): HasManyThrough
    {
        return $this->hasManyThrough(
            TaxonConcept::class,
            TaxonConceptMapping::class,
            'subject_taxon_concept_id', // Foreign key on Mapping table
            'id',                 // Foreign key on target Concept
            'id',                 // Local key on this Concept
            'object_concept_id'   // Local key on Mapping table
        )->whereHas('mappingRelation', function ($query) use ($mappingCode) {
            $query->where('code', $mappingCode)
                  ->whereHas('vocabulary', fn($v) => $v->where('code', 'MAPPING_RELATION'));
        });
    }
}