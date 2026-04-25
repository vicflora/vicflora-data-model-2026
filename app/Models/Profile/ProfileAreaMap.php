<?php

namespace App\Models\Profile;

use App\Models\Geography\AreaCode;
use App\Models\Geography\ThreatStatus;
use App\Models\Shared\Agent;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\Reference;
use App\Models\Taxonomy\TaxonTree;
use App\Models\Traits\Auditable;
use App\Models\Traits\Sourceable;
use App\Observers\ProfileAreaMapObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class ProfileAreaMap
 *
 * Represents the mapping of profiles to geographic areas, capturing the
 * occurrence and threat status of taxa in specific regions. This model is based
 * on the 'profile_area_map' database table, which captures the association
 * between profiles and their geographic distributions.
 *
 * The model includes relationships to the Profile, Area, TaxonTree, and various
 * controlled terms that define occurrence and threat status.
 *
 * @property int $id
 * @property int $profile_id
 * @property int $taxon_tree_id
 * @property int $area_code_id
 * @property string|null $locality
 * @property int|null $occurrence_status_id
 * @property int|null $establishment_means_id
 * @property int|null $degree_of_establishment_id
 * @property int|null $threat_status_id
 * @property bool|null $is_endemic
 * @property bool|null $has_introduced_occurrences
 * @property string|null $event_date
 * @property string|null $occurrence_remarks
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Profile $profile
 * @property-read AreaCode $areaCode
 * @property-read TaxonTree $taxonTree
 * @property-read Reference|null $source
 * @property-read ControlledTerm|null $occurrenceStatus
 * @property-read ControlledTerm|null $establishmentMeans
 * @property-read ControlledTerm|null $degreeOfEstablishment
 * @property-read ThreatStatus|null $threatStatus
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
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
    'source_id',
    'event_date',
    'occurrence_remarks',
    'created_by_id',
    'updated_by_id',
])]
#[ObservedBy(ProfileAreaMapObserver::class)]
class ProfileAreaMap extends Model
{
    use Auditable, Sourceable;

    protected $casts = [
        'is_endemic' => 'boolean',
        'has_introduced_occurrences' => 'boolean',
    ];

    /**
     * Relationships
     */

    #[BelongsTo(
        related: Profile::class, 
        foreignKey: 'profile_id', 
        ownerKey: 'taxon_concept_id'
    )]
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'taxon_concept_id');
    }
    
    /**
     * The area the profile is mapped to.
     */
    #[BelongsTo(
        related: AreaCode::class, 
        foreignKey: 'area_code_id'
    )]
    public function areaCode(): BelongsTo
    {
        return $this->belongsTo(AreaCode::class, 'area_code_id');
    }

    /**
     * The taxon tree acts as a namespace for the profile, allowing us to link 
     * to different trees if needed
     */
    #[BelongsTo(
        related: TaxonTree::class, 
        foreignKey: 'taxon_tree_id'
    )]
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class, 'taxon_tree_id');
    }

    // Controlled Term lookups using your new ID-by-Code logic

    #[BelongsTo(
        related: ControlledTerm::class, 
        foreignKey: 'occurrence_status_id'
    )]
    public function occurrenceStatus(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'occurrence_status_id');
    }

    #[BelongsTo(
        related: ControlledTerm::class, 
        foreignKey: 'establishment_means_id'
    )]
    public function establishmentMeans(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'establishment_means_id');
    }

    #[BelongsTo(
        related: ControlledTerm::class, 
        foreignKey: 'degree_of_establishment_id'
    )]
    public function degreeOfEstablishment(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'degree_of_establishment_id');
    }

    #[BelongsTo(
        related: ThreatStatus::class, 
        foreignKey: 'threat_status_id'
    )]
    public function threatStatus(): BelongsTo
    {
        return $this->belongsTo(ThreatStatus::class, 'threat_status_id');
    }
}