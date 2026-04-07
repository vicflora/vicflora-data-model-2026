<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\HasSidecar;
use App\Models\Traits\HasUsages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class VernacularName
 *
 * Represents a vernacular name, which is a specific type of taxonomic name.
 * This model is based on the 'vernacular_names' database view, which combines 
 * data from the 'taxon_names' table and its related extension for vernacular 
 * names.
 *
 * The model includes relationships to the rank (ControlledTerm) and external
 * identities.
 * 
 * @property int $id
 * @property string $guid
 * @property string $name_string
 * @property string|null $language
 * @property int|null $rank_id
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property-read TaxonName $taxonName
 * @property-read ControlledTerm|null $rank
 * @property-read Collection<int, ExternalIdentity>|null $externalIdentities
 * @property-read Collection<int, TaxonNameUsageMap> $usages
 * 
 */
#[Table(
    name: 'vernacular_names', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'guid',
    'name_string',
    'language',
    'rank_id',
    'created_by_id',
    'updated_by_id',
])]
class VernacularName extends Model
{
    use HasSidecar, HasUsages;

    /**
     * Define the relationship to the taxon name.
     * 
     * @return BelongsTo
     */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class);
    }
    
    public function getBaseTable(): string
    {
        return 'taxon_names';
    }

    public function getBaseModelClass(): string
    {
        return TaxonName::class;
    }

    public function getExtensionTable(): string
    {
        return 'vernacular_names_ext';
    }

    public function getSidecarFields(): array
    {
        return [];
    }

    #[\Override]
    protected function getSidecarForeignKey(): string
    {
        return 'taxon_name_id';
    }
}
