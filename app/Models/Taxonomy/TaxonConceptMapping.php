<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\Reference;
use App\Models\Shared\User;
use App\Models\Traits\Auditable;
use App\Models\Traits\Createable;
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
 * @property jsonb|null $metadata
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
    'metadata',
    'remarks'
])]
class TaxonConceptMapping extends Model
{
    use Auditable, Sourceable, Createable;

    protected $casts = [
        'metadata' => 'array',
    ];

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
}
