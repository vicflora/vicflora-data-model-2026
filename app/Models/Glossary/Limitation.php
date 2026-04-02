<?php

namespace App\Models\Glossary;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'limitations', schema: 'glossary', incrementing: true)]
#[Fillable([
    'name',
    'version',
    'created_by_id',
    'updated_by_id',
])]
class Limitation extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * Terms that are restricted by this limitation.
     */
    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class, 'glossary.term_limitation_map', 'limitation_id', 'term_id');
    }

    /**
     * Relationships that are only valid under this limitation.
     */
    public function termRelationships(): HasMany
    {
        return $this->hasMany(TermRelationship::class, 'limitation_id');
    }
}