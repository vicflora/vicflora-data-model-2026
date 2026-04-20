<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\Reference;
use App\Models\Shared\User;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use App\Models\Traits\Sourceable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Represents a mapping between two TaxonConcepts, indicating a relationship 
 * such as equivalence, inclusion, etc.
 * This model captures the nature of the relationship, the component of the 
 * taxon concepts the mapping is based on, the method used to determine it, and 
 * the source reference.
 * 
 * @property int $id
 * @property string $guid
 * @property int $subject_taxon_concept_id
 * @property int $object_taxon_concept_id
 * @property int $mapping_relation_id
 * @property int|null $taxon_concept_component_id
 * @property int|null $mapping_method_id
 * @property string|null $remarks
 * @property int|null $source_id
 * @property int|null $creator_id
 * @property Carbon|null $created
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read ControlledTerm $mappingRelation
 * @property-read TaxonConcept $subjectTaxonConcept
 * @property-read TaxonConcept $objectTaxonConcept
 * @property-read ControlledTerm|null $taxonConceptComponent
 * @property-read ControlledTerm|null $mappingMethod
 * @property-read Reference|null $source
 * @property-read User|null $creator
 * @property-read Collection<int, TaxonConceptMapping> $mappings
 * @property-read Collection<int, TaxonConcept> $isCongruentWith
 * @property-read Collection<int, TaxonConcept> $includes
 * @property-read Collection<int, TaxonConcept> $isIncludedIn
 * @property-read Collection<int, TaxonConcept> $partiallyOverlaps
 * @property-read Collection<int, TaxonConcept> $isDisjointWith
 * @property-read Collection<int, TaxonConcept> $intersects
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
#[Table(
    name: 'taxon_concept_mappings', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'guid',
    'subject_taxon_concept_id',
    'object_taxon_concept_id',
    'mapping_relation_id',
    'taxon_concept_component_id',
    'mapping_method_id',
    'source_id',
    'creator_id',
    'remarks'
])]
class TaxonConceptMapping extends Model
{
    use Blameable, IncrementsVersion, Sourceable;

    /**
     * The type of relationship between the subject and object concepts, 
     * strictly scoped to the TAXON_CONCEPT_MAPPING_RELATION vocabulary.
     */
    public function mappingRelation(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'mapping_relation_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'MAPPING_RELATION');
            });
    }
    
    /**
     * The subject TaxonConcept in the mapping relationship.
     * 
     * @return BelongsTo
     */
    public function subjectTaxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'subject_taxon_concept_id');
    }

    /**
     * The object TaxonConcept in the mapping relationship.
     * 
     * @return BelongsTo
     */
    public function objectTaxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'object_taxon_concept_id');
    }


    /**
     * The component of the TaxonConcept to which the mapping applies, 
     * strictly scoped to the TAXON_CONCEPT_COMPONENT vocabulary.
     * 
     * @return BelongsTo
     */
    public function taxonConceptComponent(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'taxon_concept_component_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'TAXON_CONCEPT_COMPONENT');
            });
    }

    /**
     * The method used to determine the mapping, 
     * strictly scoped to the MAPPING_METHOD vocabulary.
     * 
     * @return BelongsTo
     */
    public function mappingMethod(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'mapping_method_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'MAPPING_METHOD');
            });
    }

    /**
     * The Agent who created the mapping.
     * 
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
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
