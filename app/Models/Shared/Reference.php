<?php

namespace App\Models\Shared;

use App\Models\Geography\Gazetteer;
use App\Models\Geography\ThreatStatusAuthority;
use App\Models\Taxonomy\Protologue;
use App\Models\Taxonomy\Taxonomy;
use App\Models\Taxonomy\TaxonomyVersion;
use App\Models\Taxonomy\Treatment;
use App\Models\Taxonomy\TreatmentVersion;
use App\Models\Taxonomy\Typification;
use App\Models\Traits\Auditable;
use App\Services\ReferenceFormatter;
use App\Traits\ManagesSidecars;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class Reference
 *
 * Represents a reference, which is a bibliographic citation that may be
 * associated with various entities in the application. This model is based on
 * the 'references_view' database view, which aggregates data from multiple
 * tables to provide a comprehensive representation of references and their
 * roles.
 *
 * The model includes relationships to the reference type (ControlledTerm) and
 * provides helper methods to check for specific roles and types based on the
 * aggregated data from the view.
 *
 * @property int $id
 * @property int|null $reference_type_id
 * @property string|null $author_string
 * @property string|null $year
 * @property string|null $title
 * @property string|null $doi
 * @property string|null $uri
 * @property array|null $metadata
 * @property string|null $reference_role
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read ControlledTerm|null $type
 * @property-read array<string> $roleList
 * 
 * @property Treatment|null $treatment
 * @property TreatmentVersion|null $treatmentVersion
 * @property Protologue|null $protologue
 * @property Taxonomy|null $taxonomy
 * @property TaxonomyVersion|null $taxonomyVersion
 * @property Gazetteer|null $gazetteer
 * @property ThreatStatusAuthority|null $threatStatusAuthority
 * @property ExternalIdentityAuthority|null $externalIdentityAuthority
 * 
 * @property-read Collection<int, Agent> $contributors
 * @property-read Collection<int, Agent> $authors
 * @property-read Collection<int, Agent> $editors
 * @property-read Reference|null $container
 * @property-read Collection<int, Reference> $items
 * @property-read Collection<int, Agent> $authorsForCitation
 * @property-read string|null $citationAuthorshipString
 * @property-read string|null $shortCitation
 * @property-read string $fullReference
 * @property-read Agent|null $createdBy
 * @property-read Agent|null $updatedBy
 */
#[Table(
    name: 'references', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'guid',
    'reference_role',
    'reference_type_id',
    'author_string',
    'year',
    'full_reference_string',
    'short_citation_string',
    'title',
    'doi',
    'uri',
    'metadata',
    'created_at',
    'updated_at',
])]
class Reference extends Model
{
    use Auditable, ManagesSidecars;

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Default attributes for the model.
     * 
     * @return array
     */
    protected $attributes = [
        'reference_role' => 'GENERAL',
    ];

    /**
     * Get the base table name for the sidecars.
     * This is used by the ManagesSidecars trait to know which table to write to for the main reference data.
     * 
     * @return string
     */
    protected function baseTable(): string
    {
        return 'references';
    }

    protected function baseTableFields(): array
    {
        return [
            'id', 
            'guid', 
            'reference_role', 
            'reference_type_id',
            'parent_id',
            'title', 
            'year', 
            'author_string', 
            'full_reference_string', 
            'short_citation_string', 
            'metadata'
        ];
    }

    /**
     * Select sidecar model based on the reference_role attribute.
     * 
     * This is used by the ManagesSidecars trait.
     *
     * @param array $attributes
     * @return void
     */
    public function selectSidecarModel(array $attributes = [])
    {
        $role = $attributes['reference_role'] ?? $this->reference_role ?? 'GENERAL';

        return match($role) {
            'PROTOLOGUE' => Protologue::findOrNew($this->id),
            'TYPIFICATION' => Typification::findOrNew($this->id),
            'TREATMENT' => Treatment::findOrNew($this->id),
            'TREATMENT_VERSION' => TreatmentVersion::findOrNew($this->id),
            'TAXONOMY' => Taxonomy::findOrNew($this->id),
            'TAXONOMY_VERSION' => TaxonomyVersion::findOrNew($this->id),
            'GAZETTEER' => Gazetteer::findOrNew($this->id),
            'THREAT_STATUS_AUTHORITY' => ThreatStatusAuthority::findOrNew($this->id),
            'EXTERNAL_IDENTITY_AUTHORITY' => ExternalIdentityAuthority::findOrNew($this->id),
            default => null,
        };
    }

    /**
     * Relationship to the Vocabulary Layer (Layer 8)
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'reference_type_id');
    }

    /*
     * Sidecar relationships
     */

    /**
     * Treatment sidecar
     *
     * @return HasOne
     */
    public function treatment(): HasOne
    {
        return $this->hasOne(Treatment::class, 'id');
    }

    /**
     * Treatment Version sidecar
     *
     * @return HasOne
     */
    public function treatmentVersion(): HasOne
    {
        return $this->hasOne(TreatmentVersion::class, 'id');
    }

    /**
     * Protologue sidecar
     *
     * @return HasOne
     */
    public function protologue(): HasOne
    {
        return $this->hasOne(Protologue::class, 'id');
    }

    /**
     * Typification sidecar
     *
     * @return HasOne
     */
    public function typification(): HasOne
    {
        return $this->hasOne(Typification::class, 'id');
    }

    /**
     * Taxonomy sidecar
     *
     * @return HasOne
     */
    public function taxonomy(): HasOne
    {
        return $this->hasOne(Taxonomy::class, 'id');
    }

    /**
     * Taxonomy Version sidecar
     *
     * @return HasOne
     */
    public function taxonomyVersion(): HasOne
    {
        return $this->hasOne(TaxonomyVersion::class, 'id');
    }

    /**
     * Gazetteer sidecar
     *
     * @return HasOne
     */
    public function gazetteer(): HasOne
    {
        return $this->hasOne(Gazetteer::class, 'id');
    }

    /**
     * Threat Status Authority sidecar
     *
     * @return HasOne
     */
    public function threatStatusAuthority(): HasOne
    {
        return $this->hasOne(ThreatStatusAuthority::class, 'id');
    }

    /**
     * External Identity Authority sidecar
     *
     * @return HasOne
     */
    public function externalIdentityAuthority(): HasOne
    {
        return $this->hasOne(ExternalIdentityAuthority::class, 'id');
    }

    /**
     * All contributors ordered by sequence.
     */
    public function contributors(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'reference_contributors_map')
            ->using(ReferenceContributorMap::class)
            ->withPivot(['contributor_role_id', 'sequence'])
            ->orderBy('pivot_sequence');
    }

    /**
     * Specifically the Authors (used for short citations).
     */
    public function authors(): BelongsToMany
    {
        return $this->contributors()
            ->wherePivot('contributor_role_id', ControlledTerm::getIdByCode('CONTRIBUTOR_ROLE', 'AUTHOR'));
    }

    /**
     * Specifically the Editors (used for "In: Editor (ed.)" formatting).
     */
    public function editors(): BelongsToMany
    {
        return $this->contributors()
            ->wherePivot('contributor_role_id', ControlledTerm::getIdByCode('CONTRIBUTOR_ROLE', 'EDITOR'));
    }

    /**
     * Relationship: The parent container (e.g., the Journal for an Article).
     * Usage: $article->container->title
     */
    public function container(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'parent_id');
    }

    /**
     * Relationship: The child items (e.g., the Articles within a Journal).
     * Usage: $journal->items->count()
     */
    public function items(): HasMany
    {
        return $this->hasMany(Reference::class, 'parent_id');
    }

    /**
     * Accessor: Get the appropriate collection of agents for a citation.
     * Prioritizes Authors, then Editors, then looks to the Container.
     * @return Attribute
     */
    protected function authorsForCitation(): Attribute
    {
        return Attribute::make(
            get: function (): Collection {
                // 1. Try local authors
                if ($this->authors->isNotEmpty()) {
                    return $this->authors;
                }

                // 2. Try local editors (common for edited volumes/books)
                if ($this->editors->isNotEmpty()) {
                    return $this->editors;
                }

                // 3. Fallback to the container (Journal/Book) if this is an item
                if ($this->container) {
                    if ($this->container->authors->isNotEmpty()) {
                        return $this->container->authors;
                    }
                    return $this->container->editors;
                }

                return new Collection();
            }
        );
    }

    /**
     * Accessor: The formatted author string for the full reference.
     * Follows Chicago: "Lastname, Initials, Lastname, Initials & Lastname, Initials"
     */
    protected function citationAuthorString(): Attribute
    {
        return Attribute::make(
            get: function () {
                $agents = $this->authorship_for_citation; // Using the collection logic from before
                $count = $agents->count();

                if ($count === 0) return 'Anon.';

                $formattedNames = $agents->map(fn($agent) => $agent->full_bibliographic_name);

                if ($count === 1) {
                    return $formattedNames[0];
                }

                if ($count === 2) {
                    return "{$formattedNames[0]} & {$formattedNames[1]}";
                }

                // For 3 or more: "Name, Name & Name"
                $last = $formattedNames->pop();
                return $formattedNames->implode(', ') . " & " . $last;
            }
        );
    }

    /**
     * Short, Chicago-style, citation
     * @return Attribute
     */
    protected function shortCitation(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->short_citation_string 
                ?? app(ReferenceFormatter::class)->formatShortCitation($this)
        );
    }

    /**
     * Accessor: The full markdown reference string.
     * @property-read string $fullReference
     */
    protected function fullReference(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_reference_string 
                ?? app(ReferenceFormatter::class)->format($this)
        );
    }

    /**
     * In App\Models\Shared\Reference.php
     */
    protected function runPrePersistLogic()
    {
        $formatter = app(ReferenceFormatter::class);

        // Generate strings for the 'references_base' table
        // We access attributes directly on $this because the trait already called fill()
        $this->author_string = $this->citation_authorship_string;
        $this->full_reference_string = $formatter->format($this);
        $this->short_citation_string = $formatter->formatShortCitation($this);
    }
}