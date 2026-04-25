<?php

namespace App\Models\Geography;

use App\Models\Geography\Area;
use App\Models\Geography\ThreatStatusAuthority;
use App\Models\Shared\ControlledTerm;
use App\Models\Taxonomy\TaxonName;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ThreatStatus
 *
 * Represents the threat status of a taxon, which includes information about the
 * scientific name, legal authority, geographic jurisdiction, and standardized
 * status. This model is based on the 'threat_statuses' database table, which
 * captures the threat status information for taxa.
 *
 * The model includes relationships to the scientific name (TaxonName), legal
 * authority (ThreatStatusAuthority), geographic area (Area), and standardized
 * status (ControlledTerm). It also includes a JSONB field for metadata and a
 * text field for remarks.
 *
 * @property int $id
 * @property int $scientific_name_id
 * @property int $threat_status_authority_id
 * @property int $area_id
 * @property int $status_term_id
 * @property array|null $metadata
 * @property string|null $remarks
 * 
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read TaxonName $scientificName
 * @property-read ThreatStatusAuthority $authority
 * @property-read Area $area
 * @property-read ControlledTerm $status
 */
#[Table(
    name: 'threat_statuses', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'scientific_name_id',
    'threat_status_authority_id',
    'area_id',
    'status_term_id',
    'metadata',
    'remarks',
    'created_by_id',
    'updated_by_id',
    'created_at',
    'updated_at',
])]
class ThreatStatus extends Model
{
    use Auditable;

    /**
     * Cast JSON metadata to an array for easy manipulation.
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The Nomenclatural anchor (from your scientific_names_ext / taxon_names).
     */
    #[BelongsTo(
        related: TaxonName::class, 
        foreignKey: 'scientific_name_id'
    )]
    public function scientificName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'scientific_name_id');
    }

    /**
     * The Legal Authority (Sidecar of References).
     */
    #[BelongsTo(
        related: ThreatStatusAuthority::class, 
        foreignKey: 'threat_status_authority_id'
    )]
    public function authority(): BelongsTo
    {
        return $this->belongsTo(ThreatStatusAuthority::class, 'threat_status_authority_id');
    }

    /**
     * The Geographic Jurisdiction.
     */
    #[BelongsTo(
        related: Area::class, 
        foreignKey: 'area_id'
    )]
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * The standardized status (EN, VU, CR) from controlled_terms.
     */
    #[BelongsTo(
        related: ControlledTerm::class, 
        foreignKey: 'status_term_id'
    )]
    public function status(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'status_term_id');
    }
}