<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class TaxonConceptLabel
 *
 * @property int $id
 * @property string $guid
 * @property string $name_string
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
#[Table(name: 'taxon_concept_labels', key: 'id', incrementing: false)]
class TaxonConceptLabel extends Model
{
    use HasSidecar;

    protected $fillable = [
        'id',
        'base_name_id',
        'taxon_concept_id',
    ];

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

    // --- HasSidecar Implementation ---

    /**
     * Get the name of the base table that this model is based on.
     *
     * @return string
     */
    public function getBaseTable(): string
    {
        return 'taxon_names';
    }

    /**
     * Get the class name of the base model that this model extends.
     *
     * @return string
     */
    public function getBaseModelClass(): string
    {
        return TaxonName::class;
    }

    /**
     * Get the name of the sidecar extension table that holds additional fields.
     *
     * @return string
     */
    public function getExtensionTable(): string
    {
        return 'taxon_concept_labels_ext';
    }

    public function getSidecarFields(): array
    {
        return [
            'base_name_id',
            'taxon_concept_id',
        ];
    }

    #[\Override]
    protected function getSidecarForeignKey(): string
    {
        return 'taxon_name_id';
    }
}