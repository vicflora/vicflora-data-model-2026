<?php

namespace App\Models\Glossary;

use App\Models\Profile\Image;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'terms', schema: 'glossary', incrementing: true)]
#[Fillable([
    'glossary_id',
    'category_id',
    'name',
    'definition',
    'scope',
    'is_discouraged',
    'local_id',
    'language',
    'name_addendum',
    'version',
    'created_by_id',
    'updated_by_id',
])]
class Term extends Model
{
    use Blameable, IncrementsVersion;


    /**
     * The Glossary this term belongs to.
     * 
     * @return BelongsTo
     */
    public function glossary(): BelongsTo
    {
        return $this->belongsTo(Glossary::class, 'glossary_id');
    }

    /**
     * The category this term belongs to.
     * 
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * The Thesaurus Logic: Relationships between terms.
     * 
     * @return BelongsToMany
     */
    public function relatedTerms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class, 'glossary.term_relationships', 'term_id', 'related_term_id')
            ->withPivot([
                'relationship_type_id', 
                'limitation_id', 
                'is_misapplied', 
                'is_discouraged'
            ])
            ->withTimestamps();
    }

    /**
     * Geographical or Taxonomic limitations for this term.
     */
    public function limitations(): BelongsToMany
    {
        return $this->belongsToMany(Limitation::class, 'glossary.term_limitation_map', 'term_id', 'limitation_id');
    }

    /**
     * The direct images for this term, via the TermImage map.
     * * @return BelongsToMany
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'glossary.term_images', 'term_id', 'image_id')
            ->withPivot([
                'id',
                'figure',
                'version',
                'created_by_id',
                'updated_by_id'
            ])
            ->withTimestamps();
    }

    /**
     * Access to the Map entities themselves if you need the full blameable trail.
     * * @return HasMany
     */
    public function termImages(): HasMany
    {
        return $this->hasMany(TermImageMap::class, 'term_id');
    }
}