<?php

namespace App\Models\Glossary;

use App\Models\Shared\Agent;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Category
 *
 * Represents a category in a glossary, which is a grouping of terms based on 
 * shared characteristics or themes. This model is based on the 'categories' 
 * database table, which captures the details of each category in a glossary.
 *
 * The model includes relationships to the Glossary it belongs to, the defining 
 * Term (if any), and the Terms that are grouped under this category.
 * 
 * @property int $id
 * @property int $glossary_id
 * @property int|null $term_id
 * @property string $name
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Glossary $glossary
 * @property-read Term|null $term
 * @property-read Collection<int, Term> $terms
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'glossary.categories', key: 'id', incrementing: true)]
#[Fillable([
    'glossary_id',
    'term_id',
    'name',
    'version',
    'created_by_id',
    'updated_by_id',
])]
class Category extends Model
{
    use Auditable;

    /**
     * The Glossary this category belongs to.
     */
    public function glossary(): BelongsTo
    {
        return $this->belongsTo(Glossary::class, 'glossary_id');
    }

    /**
     * The specific Term that defines this category.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    /**
     * All Terms grouped under this category.
     */
    public function terms(): HasMany
    {
        return $this->hasMany(Term::class, 'category_id');
    }
}