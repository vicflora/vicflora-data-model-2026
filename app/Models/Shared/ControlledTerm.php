<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'controlled_terms')]
#[Fillable([
    'controlled_vocabulary_id',
    'label',
    'code', // Standardized on 'code'
    'iri',
    'description',
    'sort_order',
])]
class ControlledTerm extends Model
{
    protected static array $idCache = [];

    public function vocabulary(): BelongsTo
    {
        return $this->belongsTo(ControlledVocabulary::class, 'controlled_vocabulary_id');
    }

    /**
     * Scope: Filter terms by the parent vocabulary code.
     */
    public function scopeInVocabulary(Builder $query, string $vocabularyCode): Builder
    {
        return $query->whereHas('vocabulary', function ($q) use ($vocabularyCode) {
            $q->where('code', $vocabularyCode);
        });
    }

    /**
     * Get a term ID by its code and vocabulary code with static caching.
     * Usage: ControlledTerm::getIdByCode('VOUCHER_TYPE', 'CITED')
     */
    public static function getIdByCode(string $vocabCode, string $termCode): ?int
    {
        $cacheKey = "{$vocabCode}_{$termCode}";

        if (isset(static::$idCache[$cacheKey])) {
            return static::$idCache[$cacheKey];
        }

        $id = self::inVocabulary($vocabCode)
            ->where('code', $termCode)
            ->value('id');

        if ($id) {
            static::$idCache[$cacheKey] = $id;
        }

        return $id;
    }
}