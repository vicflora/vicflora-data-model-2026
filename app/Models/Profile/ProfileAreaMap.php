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

/**
 * Class ProfileAreaMap
 *
 * Represents the mapping of profiles to geographic areas, capturing the occurrence 
 * and threat status of taxa in specific regions. This model is based on the 
 * 'profile_area_map' database table, which captures the association between profiles 
 * and their geographic distributions.
 *
 * The model includes relationships to the Profile, Area, TaxonTree, and various 
 * controlled terms that define occurrence and threat status.
 * 
 * @property int $id
 * @property int $profile_id
 * @property int $taxon_tree_id
 * @property int $area_id
 * @property int $gazetteer_id
 * @property string|null $locality
 * @property int|null $occurrence_status_id
 * @property int|null $establishment_means_id
 * @property int|null $degree_of_establishment_id
 * @property int|null $threat_status_id
 * @property bool|null $is_endemic
 * @property bool|null $has_introduced_occurrences
 * @property int|null $source_id
 * @property string|null $event_date
 * @property string|null $occurrence_remarks
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read Profile $profile
 * @property-read Area $area
 * @property-read TaxonTree $taxonTree
 * @property-read ThreatStatusAuthority|null $threatStatusAuthority
 * @property-read Reference|null $source
 * @property-read ControlledTerm|null $occurrenceStatus
 * @property-read ControlledTerm|null $threatStatus
 */
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