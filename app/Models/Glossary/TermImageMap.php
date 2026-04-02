<?php

namespace App\Models\Glossary;

use App\Models\Profile\Image;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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