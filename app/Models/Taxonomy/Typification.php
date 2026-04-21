<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Reference;
use App\Models\Traits\IsSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Typification
 *
 * Represents a typification that is separate from the publication, e.g.,
 * lectotypification or conserved type. This model is based on the
 * 'typifications' view, which combines data from the 'references' table with
 * extension data (which is nothing for typifications at the moment).
 *
 * The model includes relationships to the ase Reference model and any sidecar
 * data.
 * 
 * @property int $id
 *
 * @property-read Reference $reference
 */
#[Table(
    name: 'typification_ext', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class Typification extends Model
{
    use IsSidecar;

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return [];
    }

    /**
     * Define the relationship to the Reference model.
     *
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'id', 'id');
    }

}
