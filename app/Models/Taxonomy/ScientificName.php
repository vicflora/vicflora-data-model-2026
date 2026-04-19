<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Traits\HasSidecar;
use App\Models\Traits\HasUsages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;


/**
 * Class ScientificName
 *
 * Represents a scientific name, which is a specific type of taxonomic name.
 * This model is based on the 'scientific_names' database view, which combines 
 * data from the 'taxon_names' table and its related extension for scientific 
 * names.
 *
 * The model includes relationships to the rank (ControlledTerm), nomenclatural 
 * code, nomenclatural status, and various name relations (basionym, replaced 
 * name, based on, later homonym of, conserved against, rejected against).
 * It also includes a relationship to the nomenclatural types for which this 
 * name is the typified name.
 * 
 * @property int $id
 * @property string $guid
 * @property string $name_string
 * @property string|null $language
 * @property int|null $rank_id
 * @property string|null $authorship
 * @property string|null $published_in_string
 * @property string|null $microreference
 * @property string|null $year
 * @property int|null $published_in_id
 * @property int|null $nomenclatural_code_id
 * @property int|null $nomenclatural_status_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read TaxonName $taxonName
 * @property-read ControlledTerm|null $rank
 * @property-read ControlledTerm|null $nomenclaturalCode
 * @property-read ControlledTerm|null $nomenclaturalStatus
 * @property-read ScientificName|null $basionym
 * @property-read ScientificName|null $replacedName
 * @property-read ScientificName|null $basedOn
 * @property-read ScientificName|null $laterHomonymOf
 * @property-read Collection<int, TaxonNameUsageMap> $usages
 * @property-read Collection<int, ScientificName>|null $conservedAgainst
 * @property-read Collection<int, ScientificName>|null $rejectedAgainst
 * @property-read Collection<int, NomenclaturalType>|null $typification
 * 
 * @property-read Collection<int, ScientificNameAuthorMap> $combinationAuthors
 * @property-read Collection<int, ScientificNameAuthorMap> $basionymAuthors
 * @property-read Collection<int, ScientificNameAuthorMap> $combinationAscribedAuthors
 * @property-read Collection<int, ScientificNameAuthorMap> $basionymAscribedAuthors
 * 
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
#[Table(
    name: 'scientific_names', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'guid',
    'name_string',
    'language',
    'rank_id',
    'created_by_id',
    'updated_by_id',
    'authorship',
    'published_in_string',
    'microreference',
    'year',
    'published_in_id',
    'nomenclatural_code_id',
    'nomenclatural_status_id',
])]
class ScientificName extends Model
{
    use HasSidecar, HasUsages;

    /**
    * Define the relationship to the base TaxonName.
    * This allows us to access the underlying TaxonName record for this scientific name.
    * 
    * @return BelongsTo
    */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'id');
    }
    
    /**
     * Get the nomenclatural code for the name.
     * 
     * @return BelongsTo
     */
    public function nomenclaturalCode(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'nomenclatural_code_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'NOMENCLATURAL_CODE');
        });
    }

    /**
     * Get the nomenclatural status for this name.
     * 
     * @return BelongsTo
     */
    public function nomenclaturalStatus(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'nomenclatural_status_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'NOMENCLATURAL_STATUS');
        });
    }

    /**
     * Get the Basionym.
     * * We go: ScientificName -> NameRelation -> ScientificName (as Basionym)
     * 
     * @return HasOneThrough
     */
    public function basionym(): HasOneThrough
    {
        return $this->hasOneThrough(
            ScientificName::class,
            NameRelationMap::class,
            'from_taxon_name_id',  // FK on NameRelationMap pointing to "this" name
            'id',                  // FK on TaxonName (the target)
            'id',                  // Local key on "this" name
            'to_taxon_name_id'     // Local key on NameRelationMap pointing to the Basionym
        )
        ->whereHas('relationType', function ($query) {
            $query->where('code', 'BASIONYM')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_RELATION_TYPE'));
        });
    }

    /**
     * Get the synonym for which this name is the replacement name.
     * * We go: ScientificName -> NameRelation -> ScientificName
     * 
     * @return HasOneThrough
     */
    public function replacedName(): HasOneThrough
    {
        return $this->hasOneThrough(
            ScientificName::class,
            NameRelationMap::class,
            'from_taxon_name_id',       // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'to_taxon_name_id'          // Local key on NameRelationMap pointing to the Basionym
        )
        ->whereHas('relationType', function ($query) {
            $query->where('code', 'REPLACED_NAME')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_RELATION_TYPE'));
        });
    }

    /**
     * Get the name this name is based on.
     * * We go: ScientificName -> NameRelation -> ScientificName
     * 
     * @return HasOneThrough
     */
    public function basedOn(): HasOneThrough
    {
        return $this->hasOneThrough(
            ScientificName::class,
            NameRelationMap::class,
            'from_taxon_name_id',       // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'to_taxon_name_id'          // Local key on NameRelationMap pointing to the Basionym
        )
        ->whereHas('relationType', function ($query) {
            $query->where('code', 'BASED_ON')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_RELATION_TYPE'));
        });
    }

    /**
     * Get the earlier name this name is a later homonym of.
     * * We go: ScientificName -> NameRelation -> ScientificName
     * 
     * @return HasOneThrough
     */
    public function laterHomonymOf(): HasOneThrough
    {
        return $this->hasOneThrough(
            ScientificName::class,
            NameRelationMap::class,
            'from_taxon_name_id',       // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'to_taxon_name_id'          // Local key on NameRelationMap pointing to the Basionym
        )
        ->whereHas('relationType', function ($query) {
            $query->where('code', 'LATER_HOMONYM_OF')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_RELATION_TYPE'));
        });
    }

    /**
     * Get the names this name is conserved against.
     * * We go: ScientificName -> NameRelation -> ScientificName
     * 
     * @return HasManyThrough
     */
    public function conservedAgainst(): HasManyThrough
    {
        return $this->hasManyThrough(
            ScientificName::class,
            NameRelationMap::class,
            'from_taxon_name_id',       // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'to_taxon_name_id'          // Local key on NameRelationMap pointing to the Basionym
        )
        ->whereHas('relationType', function ($query) {
            $query->where('code', 'CONSERVED_AGAINST')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_RELATION_TYPE'));
        });
    }

    /**
     * Get the names this name is rejected against.
     * 
     * @return HasManyThrough
     */
    public function rejectedAgainst(): HasManyThrough
    {
        return $this->hasManyThrough(
            ScientificName::class,
            NameRelationMap::class,
            'to_taxon_name_id',         // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'from_taxon_name_id'        // Local key on NameRelationMap pointing to the related name
        )
        ->whereHas('relationType', function ($query) {
            $query->where('code', 'CONSERVED_AGAINST')
                ->whereHas('vocabulary', fn($v) => $v->where('code', 'NAME_RELATION_TYPE'));
        });
    }

    /**
     * Get the nomenclatural types for which this name is the typified name.
     * 
     * @return HasMany
     */
    public function typification(): HasMany
    {
        return $this->hasMany(NomenclaturalType::class, 'type_name_id');
    }


    public function authorshipMaps(): HasMany
    {
        return $this->hasMany(ScientificNameAuthorMap::class, 'scientific_name_id', 'id')
            ->orderBy('sequence');
    }

    public function combinationAuthors(): HasMany
    {
        return $this->authorshipMaps()->where('author_role_id', 
            ControlledTerm::getIdByCode('SCIENTIFIC_NAME_AUTHOR_ROLE', 'COMBINATION'));
    }

    public function combinationAscribedAuthors(): HasMany
    {
        return $this->authorshipMaps()->where('author_role_id', 
            ControlledTerm::getIdByCode('SCIENTIFIC_NAME_AUTHOR_ROLE', 'COMBINATION_ASCRIBED'));
    }

    public function basionymAuthors(): HasMany
    {
        return $this->authorshipMaps()->where('author_role_id', 
            ControlledTerm::getIdByCode('SCIENTIFIC_NAME_AUTHOR_ROLE', 'BASIONYM'));
    }

    public function basionymAscribedAuthors(): HasMany
    {
        return $this->authorshipMaps()->where('author_role_id', 
            ControlledTerm::getIdByCode('SCIENTIFIC_NAME_AUTHOR_ROLE', 'BASIONYM_ASCRIBED'));
    }
    
    /**
     * Get the name of the base table that this model is based on.
     * This is used by the HasSidecar trait to know which table to join to for the sidecar fields.
     *
     * @return string
     */
    public function getBaseTable(): string
    {
        return 'taxon_names';
    }

    /**
     * Get the class name of the base model that this model extends.
     * This is used by the HasSidecar trait to know which model to use for the base data.
     *
     * @return string
     */
    public function getBaseModelClass(): string
    {
        return TaxonName::class;
    }

    /**
     * Get the name of the sidecar extension table that holds additional fields for this model.
     *
     * @return string
     */
    public function getExtensionTable(): string
    {
        return 'scientific_names_ext';
    }

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return [
            'authorship',
            'published_in_string',
            'microreference',
            'year',
            'published_in_id',
            'nomenclatural_code_id',
            'nomenclatural_status_id',
        ];
    }

    /**
     * Override the default foreign key for the sidecar relationship, since in this case
     * the scientific_names_ext table uses 'taxon_name_id' to link back to the taxon_names table.
     *
     * @return string
     */
    #[\Override]
    protected function getSidecarForeignKey(): string
    {
        return 'taxon_name_id';
    }
}
