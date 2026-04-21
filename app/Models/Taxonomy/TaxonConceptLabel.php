<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\HasSidecar;
use App\Models\Traits\IsSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class TaxonConceptLabel
 *
 * @property int $id
 * @property string $rank_id
 * @property int $base_name_id
 * @property int $taxon_concept_id
 * @property int $version
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read TaxonName $baseName
 * @property-read TaxonConcept $belongsTo
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
#[Table(name: 'taxon_concept_labels_ext', key: 'id', incrementing: false)]
#[Fillable([
    'id',
    'base_name_id',
    'taxon_concept_id',
])]
class TaxonConceptLabel extends Model
{
    use IsSidecar;

    protected $fillable = [
        'id',
        'base_name_id',
        'taxon_concept_id',
    ];

    /**
     * Get the fields of the sidecar.
     * 
     * This property is used by the IsSidecar trait.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return [
            'base_name_id',
            'taxon_concept_id',
        ];
    }

    /**
     * The nomenclatural name that forms the first part of the label.
     */
    public function baseName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'base_name_id');
    }

    /**
     * The concept that defines this label.
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id');
    }
}