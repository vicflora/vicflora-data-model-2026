<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Reference;
use App\Models\Traits\IsSidecar;
use App\Services\ProtologueStringFormatter;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Protologue
 *
 * Represents a protologue, which is a reference that describes the original
 * publication of a taxonomic name. This model is based on the 'protologues'
 * database view, which combines data from the 'references' table and its
 * related extension for protologues.
 *
 * The model includes relationships to the base Reference model and any sidecar
 * data.
 *
 * @property int $id
 * @property string|null $in_authors
 * @property string|null $protologueString
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Reference $reference
 */
#[Table(
    name: 'protologues_ext', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'in_authors',
    'protologue_string',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class Protologue extends Model
{
    use IsSidecar;

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return ['in_authors', 'protologue_string'];
    }

    public function runPrePersistLogic()
    {
        // Instead of an Observer, we call a Service or Action
        app(ProtologueStringFormatter::class)->format($this);
    }

    /**
     * Get the reference that this protologue belongs to.
     * 
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'id');
    }
}