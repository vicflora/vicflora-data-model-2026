<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\Reference;
use App\Models\Traits\Auditable;
use App\Observers\VernacularNameUsageMapObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;

/**
 * Class VernacularNameUsageMap
 *
 * Represents a mapping of vernacular names to taxon concepts. This model is
 * based on the 'vernacular_name_usages_map' database table, which captures the
 * application of vernacular names to taxon concepts, along with a flag
 * indicating whether the vernacular name is the preferred vernacular name for
 * the concept.
 *
 * The model includes relationships to the TaxonName being used and the
 * TaxonConcept to which it is applied
 *
 * @property int $id
 * @property int $taxon_name_id
 * @property int $taxon_concept_id
 * @property bool $is_preferred
 * @property jsonb|null $metadata
 * @property string|null $remarks
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read TaxonName $taxonName
 * @property-read TaxonConcept $taxonConcept
 * @property-read Reference|null $treatment
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
#[Table(
    name: 'vernacular_name_usages_map', 
    key: 'id',
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'taxon_name_id',
    'taxon_concept_id',
    'is_preferred',
    'metadata',
    'remarks',    
    'created_by_id',
    'updated_by_id',
])]
#[ObservedBy(VernacularNameUsageMapObserver::class)]
class VernacularNameUsageMap extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_preferred' => 'boolean',
        ];
    }

    /**
     * The Name being used.
     */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class);
    }

    /**
     * The TaxonConcept to which the name was applied.
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class);
    }

    /**
     * Get the Treatment in which this vernacular name usage is applied, if any.
     * Treatment is the TaxonConcept's according_to reference.
     *
     * @return HasOneThrough
     */
    public function treatment(): HasOneThrough
    {
        return $this->hasOneThrough(
            Reference::class,
            TaxonConcept::class,
            'id',               // Local key on TaxonConcept (matches taxon_concept_id)
            'reference_id',     // FK on Treatment (points to the Reference Hub)
            'taxon_concept_id', // FK on the UsageMap (this model)
            'according_to_id'   // FK on TaxonConcept (points to the Reference Hub)
        )
        ->whereHas('reference', function ($query) {
            $query->where('reference_role', 'TREATMENT');
        });
    }
}