<?php

namespace App\Models\Shared;

use App\Models\Taxonomy\TaxonConcept;
use App\Models\Taxonomy\TaxonName;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * Class ExternalIdentity
 *
 * Represents an external identity, which is a unique identifier for an entity
 * in an external system. This model is based on the 'external_identities'
 * database table, which captures the mapping of internal entities to their
 * corresponding identifiers in external systems.
 *
 * The model includes a relationship to the ExternalIdentityAuthority, which
 * defines the authority that manages the external identifiers, and
 * morph-to-many relationships to TaxonName and TaxonConcept, which represent
 * the taxonomic names and concepts associated with this external identity.
 *
 * @property int $id
 * @property int $external_identity_authority_id
 * @property string $external_id
 * @property string|null $external_url
 * @property Carbon|null $last_synced_at
 * @property array|null $metadata
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read ExternalIdentityAuthority $authority
 * @property-read Collection<int, TaxonName>|null $taxonNames
 * @property-read Collection<int, TaxonConcept>|null $taxonConcepts
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
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
    use Blameable, IncrementsVersion;

    protected $casts = [
        'metadata' => 'array',
    ];
    
    /**
     * Define the relationship to the ExternalIdentityAuthority model.
     * 
     * @return BelongsTo
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
