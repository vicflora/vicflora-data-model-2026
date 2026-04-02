<?php

namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Table(
    name: 'specimen_image_map',
    key: 'id',
    incrementing: true
)]
#[Fillable([
    'specimen_id',
    'image_id',
    'external_id',
    'sort_order'
])]
class SpecimenImageMap extends Pivot
{
    //
}