<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\IsSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class HorticulturalGroupName
 *
 * Represents additional information for a horticultural group name. 
 * This model is based on the 'horticultural_group_names_ext' database table.
 *
 * While currently serving primarily as a semantic flag to identify informal 
 * horticultural groups within the HortFlora system, this sidecar allows 
 * for future expansion of group-specific metadata (e.g. registry codes) 
 * without altering the core taxon_names table.
 *
 * @property int $id
 *
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read TaxonName $taxonName
 */
#[Table(
    name: 'horticultural_group_names_ext', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'created_by_id',
    'updated_by_id',
])]
class HorticulturalGroupName extends Model
{
    use IsSidecar;

    /**
     * Get sidecar fields
     * * Used in IsSidecar trait. Currently empty as the table 
     * acts as a semantic flag.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return [];
    }

    /**
     * Get the identity hub this sidecar belongs to.
     *
     * @return BelongsTo
     */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'id');
    }
}