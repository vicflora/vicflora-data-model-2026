<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Reference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaxonTreeGeographicScopeMap
 *
 * Represents the mapping of geographic scopes to taxonomic trees. This model is 
 * based on the 'taxon_tree_geographic_scope_map' database table, which captures 
 * the association between taxonomic trees and their geographic scopes.
 *
 * The model includes a relationship to the TaxonTree to which the geographic scope 
 * belongs.
 * 
 * @property int $id
 * @property int $taxon_tree_id
 * @property int $gazetteer_id
 * @property string $scope
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read TaxonTree $taxonTree
 * @property-read Gazetteer $gazetteer
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
 */
#[Table(name: 'public.taxon_tree_geographic_scope_map', primaryKey: 'id')]
#[Fillable(['taxon_tree_id', 'scope'])]
class TaxonTreeGeographicScopeMap extends Model
{
    public $timestamps = false;

    /**
     * The Taxon Tree this geographic scope belongs to.
     * 
     * @return BelongsTo
     */
    public function taxonTree(): BelongsTo
    {
        return $this->belongsTo(TaxonTree::class);
    }

    /**
     * The gazetteer reference that defines the geographic scope.
     * 
     * @return BelongsTo
     */
    public function gazetteer(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'gazetteer_id');
    }
}