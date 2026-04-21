<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\EntityIdentityMap;
use App\Models\Shared\ExternalIdentity;
use App\Models\Shared\Reference;
use App\Models\Traits\Auditable;
use App\Models\Traits\HasExternalIdentities;
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
    use Auditable, HasExternalIdentities;

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
        return $this->hasOneThrough(
            ScientificName::class,
            TaxonNameUsageMap::class,
            'taxon_concept_id',     // FK on TaxonName pointing to "this" concept
            'id',                   // FK on TaxonName (the target)
            'id',                   // Local key on "this" concept
            'taxon_name_id'         // Local key on TaxonNameUsageMap pointing to the ScientificName
        )
        ->whereHas('taxonName', fn($q) => $q->where('name_type', 'SCIENTIFIC'))
        ->whereHas('nameUsageRole', function ($query) {
            $query->where('code', 'ACCEPTED')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_USAGE_ROLE'));
        });
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
            TaxonNameUsageMap::class,
            'taxon_concept_id',
            'id',
            'id',
            'taxon_name_id'
        )
        // 2. Exclude the current concept's name
        ->where('taxon_name_id', '!=', $this->taxon_name_id)
        
        // 3. Simple column check instead of a subquery
        ->where('name_usage_role_id', $synonymRoleId);
    }

    /**
     * Vernacular names for this taxon concept.
     * @return HasManyThrough
     */
    public function vernacularNames(): HasManyThrough
    {
        $vernacularNameRoleId = ControlledTerm::getIdByCode('NAME_USAGE_ROLE', 'VERNACULAR_NAME');

        return $this->hasManyThrough(
            VernacularName::class,
            TaxonNameUsageMap::class,
            'taxon_concept_id',     // FK on TaxonName pointing to "this" concept
            'id',                   // FK on TaxonName (the target)
            'id',                   // Local key on "this" concept
            'taxon_name_id'         // Local key on TaxonNameUsageMap pointing to the VernacularName
        )
        ->whereHas('taxonName', fn($q) => $q->where('name_type', 'VERNACULAR'))
        ->where('name_usage_role_id', $vernacularNameRoleId);
    }

    /**
     * Preferred vernacular name for this taxon concept
     * @return HasOneThrough
     */
    public function preferredVernacularName(): HasOneThrough
    {
        $vernacularNameRoleId = ControlledTerm::getIdByCode('NAME_USAGE_ROLE', 'VERNACULAR_NAME');

        return $this->hasOneThrough(
            VernacularName::class,
            TaxonNameUsageMap::class,
            'taxon_concept_id',     // FK on TaxonName pointing to "this" concept
            'id',                   // FK on TaxonName (the target)
            'id',                   // Local key on "this" concept
            'taxon_name_id'         // Local key on TaxonNameUsageMap pointing to the VernacularName
        )
        ->where('is_preferred_vernacular_name', true)
        ->whereHas('taxonName', fn($q) => $q->where('name_type', 'VERNACULAR'))
        ->where('name_usage_role_id', $vernacularNameRoleId);
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
     * Taxon concept mappings for which this concept is the subject. Reverse
     * mappings will be materialized, so we do not need subject and object
     * mappings.
     * @return HasMany
     */
    public function mappings(): HasMany
    {
        return $this->hasMany(TaxonConceptMapping::class, 'subject_taxon_concept_id');
    }

    /**
     * Taxon concepts that are considered congruent with this concept.
     * @return HasManyThrough
     */
    public function isCongruentWith(): HasManyThrough
    {
        // High-performance ID lookup from the MAPPING_RELATION vocabulary
        $congruentId = ControlledTerm::getIdByCode('MAPPING_RELATION', 'IS_CONGRUENT_WITH');

        return $this->hasManyThrough(
            TaxonConcept::class,          // The Target Model
            TaxonConceptMapping::class,   // The Pivot Model
            'from_taxon_concept_id',      // Foreign key on the pivot (pointing to "this" concept)
            'id',                         // Foreign key on the target (TaxonConcept.id)
            'id',                         // Local key on "this" concept
            'to_taxon_concept_id'         // Local key on the pivot (pointing to the target concept)
        )
        ->where('mapping_relation_id', $congruentId);
    }

    /**
     * Taxon concepts that included in this concept.
     * @return HasManyThrough
     */
    public function includes(): HasManyThrough
    {
        // High-performance ID lookup from the MAPPING_RELATION vocabulary
        $congruentId = ControlledTerm::getIdByCode('MAPPING_RELATION', 'INCLUDES');

        return $this->hasManyThrough(
            TaxonConcept::class,          // The Target Model
            TaxonConceptMapping::class,   // The Pivot Model
            'from_taxon_concept_id',      // Foreign key on the pivot (pointing to "this" concept)
            'id',                         // Foreign key on the target (TaxonConcept.id)
            'id',                         // Local key on "this" concept
            'to_taxon_concept_id'         // Local key on the pivot (pointing to the target concept)
        )
        ->where('mapping_relation_id', $congruentId);
    }

    /**
     * Taxon concepts that include this concept.
     * @return HasManyThrough
     */
    public function isIncludedIn(): HasManyThrough
    {
        // High-performance ID lookup from the MAPPING_RELATION vocabulary
        $congruentId = ControlledTerm::getIdByCode('MAPPING_RELATION', 'IS_INCLUDED_IN');

        return $this->hasManyThrough(
            TaxonConcept::class,          // The Target Model
            TaxonConceptMapping::class,   // The Pivot Model
            'from_taxon_concept_id',      // Foreign key on the pivot (pointing to "this" concept)
            'id',                         // Foreign key on the target (TaxonConcept.id)
            'id',                         // Local key on "this" concept
            'to_taxon_concept_id'         // Local key on the pivot (pointing to the target concept)
        )
        ->where('mapping_relation_id', $congruentId);
    }

    /**
     * Taxon concepts with which this concept verlaps.
     * @return HasManyThrough
     */
    public function partiallyOverlaps(): HasManyThrough
    {
        // High-performance ID lookup from the MAPPING_RELATION vocabulary
        $congruentId = ControlledTerm::getIdByCode('MAPPING_RELATION', 'PARTIALLY_OVERLAPS');

        return $this->hasManyThrough(
            TaxonConcept::class,          // The Target Model
            TaxonConceptMapping::class,   // The Pivot Model
            'from_taxon_concept_id',      // Foreign key on the pivot (pointing to "this" concept)
            'id',                         // Foreign key on the target (TaxonConcept.id)
            'id',                         // Local key on "this" concept
            'to_taxon_concept_id'         // Local key on the pivot (pointing to the target concept)
        )
        ->where('mapping_relation_id', $congruentId);
    }
    
    /**
     * Taxon concepts that are disjoint from this concept.
     * @return HasManyThrough
     */
    public function isDisjointFrom(): HasManyThrough
    {
        // High-performance ID lookup from the MAPPING_RELATION vocabulary
        $congruentId = ControlledTerm::getIdByCode('MAPPING_RELATION', 'IS_DISJOINT_FROM');

        return $this->hasManyThrough(
            TaxonConcept::class,          // The Target Model
            TaxonConceptMapping::class,   // The Pivot Model
            'from_taxon_concept_id',      // Foreign key on the pivot (pointing to "this" concept)
            'id',                         // Foreign key on the target (TaxonConcept.id)
            'id',                         // Local key on "this" concept
            'to_taxon_concept_id'         // Local key on the pivot (pointing to the target concept)
        )
        ->where('mapping_relation_id', $congruentId);
    }
    
    /**
     * Taxon concepts that intersect with this concept.
     * @return HasManyThrough
     */
    public function intersects(): HasManyThrough
    {
        // High-performance ID lookup from the MAPPING_RELATION vocabulary
        $congruentId = ControlledTerm::getIdByCode('MAPPING_RELATION', 'INTERSECTS');

        return $this->hasManyThrough(
            TaxonConcept::class,          // The Target Model
            TaxonConceptMapping::class,   // The Pivot Model
            'from_taxon_concept_id',      // Foreign key on the pivot (pointing to "this" concept)
            'id',                         // Foreign key on the target (TaxonConcept.id)
            'id',                         // Local key on "this" concept
            'to_taxon_concept_id'         // Local key on the pivot (pointing to the target concept)
        )
        ->where('mapping_relation_id', $congruentId);
    }

    /**
     * Define the relationship to external identities.
     * @return MorphToMany
     */
    public function externalIdentities(): MorphToMany
    {
        return $this->morphToMany(
            ExternalIdentity::class, 
            'entity', 
            'entity_identity_map'
        )
        ->using(EntityIdentityMap::class)
        ->withTimestamps();
    }
}