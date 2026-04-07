<?php

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class ControlledTerm
 *
 * Represents a controlled term, which is a specific value within a controlled
 * vocabulary. This model is based on the 'controlled_terms' database table,
 * which captures the individual terms that belong to controlled vocabularies.
 *
 * The model includes fields for the label, code, IRI, description, and sort
 * order of the term, as well as a relationship to the parent controlled
 * vocabulary.
 *
 * @property int $id
 * @property int $controlled_vocabulary_id
 * @property string $label
 * @property string|null $code
 * @property string|null $iri
 * @property string|null $description
 * @property int|null $sort_order
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read ControlledVocabulary $vocabulary
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
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
    use Blameable, IncrementsVersion;

    /**
     * Array to hold cached Controlled Term IDs.
     *
     * @var array
     */
    protected static array $idCache = [];

    /**
     * Define the relationship to the parent vocabulary.
     * 
     * @return BelongsTo
     */
    public function vocabulary(): BelongsTo
    {
        return $this->belongsTo(ControlledVocabulary::class, 'controlled_vocabulary_id');
    }

    /**
     * Scope: Filter terms by the parent vocabulary code.
     * 
     * @param Builder $query
     * @param string $vocabularyCode
     * @return Builder
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
     * 
     * @param string $vocabCode The code of the controlled vocabulary (e.g., 'VOUCHER_TYPE').
     * @param string $termCode The code of the term within that vocabulary (e.g., 'CITED').
     * @return int|null The ID of the term, or null if not found.
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