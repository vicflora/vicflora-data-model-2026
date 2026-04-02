<?php

namespace App\Models\Shared;

use App\Models\Taxonomy\TaxonConcept;
use App\Models\Taxonomy\TaxonName;
use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Table(
    name: 'external_identities', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'external_identity_authority_id',
    'external_id',
    'external_url',
    'last_synced_at',
    'metadata',
])]
class ExternalIdentity extends Model
{
    use Blameable;

    /**
     * The Authority (IPNI, POWO, etc.)
     */
    public function authority(): BelongsTo
    {
        return $this->belongsTo(ExternalIdentityAuthority::class, 'external_identity_authority_id');
    }

    /**
     * Get all Taxon Names associated with this identity.
     */
    public function taxonNames(): MorphToMany
    {
        return $this->morphedByMany(
            TaxonName::class, 
            'entity', 
            'entity_identity_map'
        )->withTimestamps();
    }

    /**
     * Get all Taxon Concepts associated with this identity.
     */
    public function taxonConcepts(): MorphToMany
    {
        return $this->morphedByMany(
            TaxonConcept::class, 
            'entity', 
            'entity_identity_map'
        )->withTimestamps();
    }
}
