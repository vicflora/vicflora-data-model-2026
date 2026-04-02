<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\HasSidecar;
use App\Models\Traits\HasUsages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
            'from_taxon_name_id',            // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
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
            'from_taxon_name_id',            // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'to_taxon_name_id'     // Local key on NameRelationMap pointing to the Basionym
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
            'from_taxon_name_id',            // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'to_taxon_name_id'     // Local key on NameRelationMap pointing to the Basionym
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
            'from_taxon_name_id',            // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'to_taxon_name_id'     // Local key on NameRelationMap pointing to the Basionym
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
            'from_taxon_name_id',            // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'to_taxon_name_id'     // Local key on NameRelationMap pointing to the Basionym
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
            'to_taxon_name_id',            // FK on NameRelationMap pointing to "this" name
            'id',                       // FK on TaxonName (the target)
            'id',                       // Local key on "this" name
            'from_taxon_name_id'     // Local key on NameRelationMap pointing to the related name
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
    
    public function getBaseTable(): string
    {
        return 'taxon_names';
    }

    public function getBaseModelClass(): string
    {
        return TaxonName::class;
    }

    public function getExtensionTable(): string
    {
        return 'scientific_names_ext';
    }

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

    #[\Override]
    protected function getSidecarForeignKey(): string
    {
        return 'taxon_name_id';
    }
}
