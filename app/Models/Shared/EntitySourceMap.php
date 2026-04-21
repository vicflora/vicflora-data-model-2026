<?php

namespace App\Models\Shared;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class EntitySourceMap extends MorphPivot
{
    use Auditable;

    protected $table = 'entity_source_map';
    public $incrementing = true;

    protected $casts = [
        'metadata' => 'array',
    ];

    public function reference()
    {
        return $this->belongsTo(Reference::class);
    }

    public function sourceable()
    {
        return $this->morphTo();
    }
}