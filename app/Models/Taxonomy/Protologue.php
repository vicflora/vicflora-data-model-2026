<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Reference;
use App\Models\Traits\Blameable;
use App\Models\Traits\HasSidecar;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Protologue
 *
 * Represents a protologue, which is a reference that describes the original publication of a taxonomic name. 
 * This model is based on the 'protologues' database view, which combines data from the 'references' 
 * table and its related extension for protologues.
 *
 * The model includes relationships to the base Reference model and any sidecar data.
 * 
 * @property int $id
 * @property int $reference_type_id
 * @property string $author_string
 * @property int|null $year
 * @property string|null $title
 * @property string|null $doi
 * @property string|null $url
 * @property array|null $metadata
 * @property string|null $microreference
 * 
 * @property-read Reference $reference
 */
#[Table(
    name: 'protologues', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'reference_type_id',
    'author_string',
    'year',
    'title',
    'doi',
    'url',
    'metadata',
    'microreference',
])]
class Protologue extends Model
{
    use IncrementsVersion, HasSidecar;

    /**
     * Get the reference that this protologue belongs to.
     * 
     * @return BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }

    /**
     * Get the name of the base table that this model extends.
     *
     * @return string
     */
    public function getBaseTable(): string
    {
        return 'references';
    }

    /**
     * Get the class name of the base model that this model extends.
     *
     * @return string
     */
    public function getBaseModelClass(): string
    {
        return Reference::class;
    }
    
    /**
     * Get the name of the sidecar extension table that holds additional fields for this model.
     *
     * @return string
     */
    public function getExtensionTable(): string
    {
        return 'protologues_ext';
    }

    /**
     * Get the list of fields that are stored in the sidecar extension table.
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return ['microreference'];
    }
}