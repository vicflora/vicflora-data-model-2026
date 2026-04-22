<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\Reference;
use App\Models\Traits\Auditable;
use App\Observers\ScientificNameUsageMapObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;

/**
 * Class ScientificNameUsageMap
 *
 * Represents a mapping of scientific names to taxon concepts.
 * This model is based on the 'scientific_name_usages_map' database table, which 
 * captures the application of scientific names to taxon concepts, along with
 * the role of the name usage (e.g., accepted, synonym).
 *
 * The model includes relationships to the TaxonName being used, the TaxonConcept
 * to which it is applied, and the role of the name usage defined by a 
 * ControlledTerm from the NAME_USAGE_ROLE vocabulary.
 * 
 * @property int $id
 * @property int $taxon_name_id
 * @property int $taxon_concept_id
 * @property int $name_usage_role_id
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
 * @property-read ControlledTerm $nameUsageRole
 * @property-read Reference|null $treatment
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
#[Table(
    name: 'scientific_name_usages_map', 
    key: 'id',
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'taxon_name_id',
    'taxon_concept_id',
    'name_usage_role_id',
    'metadata',
    'remarks',    
    'created_by_id',
    'updated_by_id',
])]
#[ObservedBy(ScientificNameUsageMapObserver::class)]
class ScientificNameUsageMap extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
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
     * The role of the name usage, e.g., ACCEPTED, SYNONYM.
     */
    public function nameUsageRole(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'name_usage_role_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'NAME_USAGE_ROLE');
            });
    }


    /**
     * Get the treatment that contains this scientific name usage.
     *
     * This is a hasOneThrough relationship because the ScientificNameUsageMap
     * points to a TaxonConcept, which in turn points to a Treatment (via
     * according_to_id). We also need to filter the Treatment by reference_role
     * 'TREATMENT' to ensure we only get treatments.
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