<?php

namespace App\Models\Profile;

use App\Models\Geography\Area;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\Reference;
use App\Models\Taxonomy\TaxonTree;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(
    name: 'profile_area_map', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'profile_id',
    'taxon_tree_id',
    'area_id',
    'gazetteer_id',
    'locality',
    'occurrence_status_id',
    'establishment_means_id',
    'degree_of_establishment_id',
    'threat_status_id',
    'is_endemic',
    'has_introduced_occurrences',
    'source_id',
    'event_date',
    'occurrence_remarks',
    'created_by_id',
    'updated_by_id',
])]
class ProfileAreaMap extends Model
{
    protected $casts = [
        'is_endemic' => 'boolean',
        'has_introduced_occurrences' => 'boolean',
    ];

    /**
     * Relationships
     */

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'taxon_concept_id');
    }
    
    /**
     * The area the profile is mapped to.
     *
     * @return BelongsTo
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'gazetteer_id');
    }

    /**
     * The taxon tree acts as a namespace for the profile, allowing us to link 
     * to different trees if needed
     *
     * @return BelongsTo
     */
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'taxon_tree_id');
    }

    public function threatStatusAuthority(): BelongsTo
    {
        // The reference that provides the authority for the threat status
        return $this->belongsTo(ThreatStatusAuthority::class, 'threat_status_authority_id');
    }

    public function source(): BelongsTo
    {
        // The specific reference/evidence for this record
        return $this->belongsTo(Reference::class, 'source_id');
    }

    // Controlled Term lookups using your new ID-by-Code logic
    public function occurrenceStatus(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'occurrence_status_id');
    }

    public function threatStatus(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'threat_status_id');
    }
}