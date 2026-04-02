<?php

namespace App\Models\Glossary;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'categories', schema: 'glossary', incrementing: true)]
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
    use Blameable, IncrementsVersion;

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