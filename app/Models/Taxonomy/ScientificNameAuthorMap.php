<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Parsed authorship of a scientific name. This links the authorship of the
 * Scientific Name to the Authority Layer.
 * 
 * @property int $id
 * @property int $scientific_name_id
 * @property int $agent_id
 * @property int $author_role_id
 * @property int $sequence
 * @property int $version
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read ScientificNamescientificName $scientificName
 * @property-read Agent $agent
 * @property-read ControlledTerm $authorRole
 */
#[Table(name: 'scientific_name_author_map', key: 'id', incrementing: true)]
#[Fillable([
    'scientific_name_id', 
    'agent_id', 
    'author_role_id', 
    'sequence',
    'version',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class ScientificNameAuthorMap extends Model
{

    /**
     * Scientific Name the authorship of which the Agent had a role in
     *
     * @return BelongsTo
     */
    public function scientificName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'scientific_name_id')
            ->where('name_type', 'SCIENTIFIC');
    }

    /**
     * The Agent record for the author  
     *
     * @return BelongsTo
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    /**
     * Role of the individual author
     *
     * This can be COMBINATION_AUTHOR, BASIONYM_AUTHOR,
     * COMBINATION_ASCRIBED_AUTHOR or BASIONYM_ASCRIBED_AUTHOR
     *
     * @return BelongsTo
     */
    public function authorRole(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'author_role_id');
    }
}