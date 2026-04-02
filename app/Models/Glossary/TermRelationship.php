<?php

namespace App\Models\Glossary;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use App\Models\Shared\ControlledTerm;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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