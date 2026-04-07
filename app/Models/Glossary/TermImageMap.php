<?php

namespace App\Models\Glossary;

use App\Models\Image\Image;
use App\Models\Shared\Agent;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class TermImageMap
 *
 * Represents the mapping between a Term and an Image in the glossary. This 
 * model captures the association of an image with a term, including any figure 
 * information and metadata about the relationship.
 *
 * The model includes relationships to the Term it illustrates and the Image it 
 * references.
 * 
 * @property int $id
 * @property int $term_id
 * @property int $image_id
 * @property string|null $figure
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Term $term
 * @property-read Image $image
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(name: 'term_images', schema: 'glossary', incrementing: true)]
#[Fillable([
    'term_id',
    'image_id',
    'figure',
    'version',
    'created_by_id',
    'updated_by_id',
])]
class TermImageMap extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * The Term this image illustrates.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    /**
     * The actual Image entity (likely in a Shared or Media namespace).
     */
    public function image(): BelongsTo
    {
        // Assuming Image model exists in App\Models\Shared
        return $this->belongsTo(Image::class, 'image_id');
    }
}