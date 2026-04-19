<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Shared\ControlledTerm;

class EntityImageMap extends MorphPivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entity_image_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'image_id',
        'image_role_id',
        'sort_order',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Get the image associated with the map.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Get the role (caption, hero, thumbnail, etc.) of the image.
     */
    public function imageRole(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'image_role_id');
    }
}