<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
    use Blameable;

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
