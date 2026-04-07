<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\EntityIdentityMap;
use App\Models\Shared\ExternalIdentity;
use App\Models\Traits\Blameable;
use App\Models\Traits\HasUsages;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
 * @property-read ControlledTerm|null $rank
 * @property-read Collection<int, ExternalIdentity>|null $externalIdentities
 * @property-read Collection<int, TaxonNameUsageMap> $usages
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 * 
 */
#[Table(
    name: 'taxon_names_view', 
    key: 'id', 
    incrementing: true
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
])]
class TaxonName extends Model
{
    use Blameable, IncrementsVersion, HasUsages;

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
