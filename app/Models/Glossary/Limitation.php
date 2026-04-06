<?php

namespace App\Models\Glossary;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Limitation
 *
 * Represents a limitation in a glossary, which is a constraint or condition 
 * that applies to certain terms or relationships. This model is based on the 
 * 'limitations' database table, which captures the details of each limitation 
 * in a glossary.
 *
 * The model includes relationships to the Terms that are restricted by this 
 * limitation and the TermRelationships that are only valid under this 
 * limitation.
 * 
 * @property int $id
 * @property string $name
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Term[] $terms
 * @property-read \Illuminate\Database\Eloquent\Collection|TermRelationship[] $termRelationships
 */
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