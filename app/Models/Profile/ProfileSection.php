<?php

namespace App\Models\Profile;

use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Traits\Auditable;
use App\Models\Traits\Depictable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Support\Carbon;

/**
 * Class ProfileSection
 *
 * Represents a section of a profile, which contains specific information about
 * a taxonomic concept. This model is based on the 'profile_sections' database
 * table, which captures the content of profiles.
 *
 * The model includes relationships to the profile to which the section belongs
 * and the type of section (ControlledTerm) that defines the kind of information
 * in the section (e.g., Description, Biology, etc.).
 *
 * @property int $id
 * @property int $profile_id
 * @property int|null $profile_section_type_id
 * @property int|null $source_id
 * @property string|null $body_text
 * @property int $sort_order
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Profile $profile
 * @property-read ControlledTerm|null $type
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
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
    use Auditable, Depictable;

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
        return $this->belongsTo(ProfileDefItem::class, 'profile_def_item_id');
    }
}