<?php

namespace App\Models\Glossary;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TermRelationship
 *
 * Represents a relationship between two terms in a glossary. This model 
 * captures the details of how two terms are related, including the type of 
 * relationship and any limitations or qualifiers on that relationship.
 *
 * The model includes relationships to the Glossary it belongs to, the primary 
 * Term, the related Term, the type of relationship (as a ControlledTerm), and 
 * any Limitation that applies to the relationship.
 * 
 * @property int $id
 * @property int $glossary_id
 * @property int $term_id
 * @property int $related_term_id
 * @property int $relationship_type_id
 * @property int|null $limitation_id
 * @property bool $is_misapplied
 * @property bool $is_discouraged
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Glossary $glossary
 * @property-read Term $term
 * @property-read Term $relatedTerm
 * @property-read ControlledTerm $relationshipType
 * @property-read Limitation|null $limitation
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'term_relationships', schema: 'glossary', incrementing: true)]
#[Fillable([
    'glossary_id',
    'term_id',
    'related_term_id',
    'relationship_type_id',
    'limitation_id',
    'is_misapplied',
    'is_discouraged',
    'version',
    'created_by_id',
    'updated_by_id',
])]
class TermRelationship extends Model
{
    use Blameable, IncrementsVersion;

    public function glossary(): BelongsTo
    {
        return $this->belongsTo(Glossary::class, 'glossary_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    public function relatedTerm(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'related_term_id');
    }

    public function relationshipType(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'relationship_type_id');
    }

    public function limitation(): BelongsTo
    {
        return $this->belongsTo(Limitation::class, 'limitation_id');
    }
}