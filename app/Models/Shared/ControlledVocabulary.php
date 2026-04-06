<?php

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ControlledVocabulary
 *
 * Represents a controlled vocabulary, which is a standardized set of terms used for classification and annotation within the application. This model is based on the 'controlled_vocabularies' database table, which captures the details of each controlled vocabulary, including its name, code, description, and IRI (Internationalized Resource Identifier).
 *
 * The model includes a relationship to the terms (ControlledTerm) that belong to this vocabulary.
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string|null $iri
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|ControlledTerm[] $terms
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
 */
#[Table(
    name: 'controlled_vocabularies', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'name',
    'code',
    'description',
    'iri',
])]
class ControlledVocabulary extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * Get the terms belonging to this vocabulary.
     */
    public function terms(): HasMany
    {
        return $this->hasMany(ControlledTerm::class);
    }

    /**
     * Scope a query to find a vocabulary by its machine-readable code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}