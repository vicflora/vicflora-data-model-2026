<?php

namespace App\Models\Profile;

use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table(
    name: 'profile_sections',
    key: 'id',
    incrementing: true
)]
#[Fillable([
    'profile_id',
    'profile_section_type_id',
    'source_id',
    'body_text',
    'sort_order',
])]
class ProfileSection extends Model
{
    use Blameable;

    /**
     * Relationships to update when this model is updated.
     */
    protected $touches = ['profile'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'taxon_concept_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'profile_section_type_id');
    }
}