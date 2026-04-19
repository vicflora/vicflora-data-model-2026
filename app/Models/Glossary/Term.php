<?php

namespace App\Models\Glossary;

use App\Models\Media\Image;
use App\Models\Shared\Agent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\HasImages;
use App\Models\Traits\HasLimitations;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Term
 *
 * Represents a term in a glossary, which is a specific word or phrase and its
 * associated definition and metadata. This model is based on the 'terms'
 * database table, which captures the details of each term in a glossary.
 *
 * The model includes relationships to the Glossary it belongs to, its category,
 * related terms (thesaurus logic), limitations, and associated images.
 *
 * @property int $id
 * @property int $glossary_id
 * @property int|null $category_id
 * @property string $name
 * @property string|null $definition
 * @property string|null $scope
 * @property bool $is_discouraged
 * @property string|null $local_id
 * @property string|null $language
 * @property string|null $name_addendum
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Glossary $glossary
 * @property-read Category|null $category
 * @property-read Collection<int, Term> $relatedTerms
 * @property-read Collection<int, Limitation> $limitations
 * @property-read Collection<int, Image> $images
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
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
    use Blameable, IncrementsVersion, HasLimitations, HasImages;


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

}