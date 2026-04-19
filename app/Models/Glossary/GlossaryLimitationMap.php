<?php

namespace App\Models\Glossary;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class GlossaryLimitationMap extends MorphPivot
{
    protected $table = 'glossary_limitation_map';

    // This property ties it all together
    public function limitable()
    {
        return $this->morphTo();
    }
}