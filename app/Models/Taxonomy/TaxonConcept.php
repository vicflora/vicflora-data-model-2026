<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\ControlledTerm;
use App\Models\Shared\EntityIdentityMap;
use App\Models\Shared\ExternalIdentity;
use App\Models\Shared\Reference;
use App\Models\Traits\Blameable;
use App\Observers\TaxonConceptObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null     $updated_at
 * 
 * @property-read TaxonTree $taxonTree
 * @property-read TaxonName $taxonName
 * @property-read Reference $accordingTo
 * @property-read ControlledTerm $rank
 * @property-read ScientificName|null $acceptedName
 * @property-read Collection<int, ScientificName> $synonyms
 * @property-read Collection<int, VernacularName> $vernacularNames
 * @property-read VernacularName|null $preferredVernacularName
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ExternalIdentity> $externalIdentities
 * @property-read \App\Models\Shared\Agent $createdBy
 * @property-read \App\Models\Shared\Agent $updatedBy
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
    use Blameable;

    /**
     * The TaxonTree this concept belongs to.
     * 
     * @return BelongsTo
     */
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'taxon_tree_id');
    }

    /**
     * The Name being used in this specific concept.
     */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'taxon_name_id');
    }

    /**
     * The Reference ("SEC" or "Sensu") providing the context.
     */
    public function accordingTo(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'according_to_id');
    }

    /**
     * The Rank of this concept in the current taxonomic opinion.
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'rank_id')
            ->whereHas('vocabulary', fn($q) => $q->where('code', 'TAXON_RANK'));
    }

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
        ->where('taxon_name_usage_role_id', $synonymRoleId);
    }

    public function vernacularNames(): HasManyThrough
    {
        return $this->hasManyThrough(
            VernacularName::class,
            TaxonNameUsageMap::class,
            'taxon_concept_id',     // FK on TaxonName pointing to "this" concept
            'id',                   // FK on TaxonName (the target)
            'id',                   // Local key on "this" concept
            'taxon_name_id'         // Local key on TaxonNameUsageMap pointing to the VernacularName
        )
        ->whereHas('taxonName', fn($q) => $q->where('name_type', 'VERNACULAR'))
        ->whereHas('nameUsageRole', function ($query) {
            $query->where('code', 'VERNACULAR')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_USAGE_ROLE'));
        });
    }

    public function preferredVernacularName(): HasOneThrough
    {
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
        ->whereHas('nameUsageRole', function ($query) {
            $query->where('code', 'VERNACULAR')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_USAGE_ROLE'));
        });
    }

    /**
     * Define the relationship to external identities.
     *
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