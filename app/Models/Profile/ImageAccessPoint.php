<?php

namespace App\Models\Profile;

use App\Models\Shared\ControlledTerm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageAccessPoint extends Model
{
    protected $fillable = [
        'image_id',
        'variant_id',
        'access_iri',
        'format',
        'width',
        'height',
        'file_size'
    ];

    /**
     * The parent Image metadata record.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * The variant type (THUMBNAIL, PREVIEW, etc.)
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'variant_id');
    }
}