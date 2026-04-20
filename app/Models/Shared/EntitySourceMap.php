<?php

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use App\Models\Traits\IncrementsVersion;

class EntitySourceMap extends MorphPivot
{
    use Blameable, IncrementsVersion;

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