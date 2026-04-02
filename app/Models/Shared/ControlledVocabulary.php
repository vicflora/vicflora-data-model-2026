<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    protected $fillable = ['name', 'code', 'description', 'uri'];

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