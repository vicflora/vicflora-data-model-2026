<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\IsSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class HybridFormula
 *
 * Represents the additional information for a hybrid formula taxon name, which 
 * includes references to the parent names that make up the hybrid. This model 
 * is based on the 'hybrid_formulas_ext' database table, which captures the 
 * relationships between hybrid names and their parent names.
 *
 * The model uses the IsSidecar trait to link it to the main TaxonName model, 
 * allowing it to store additional fields specific to hybrid formulas without 
 * modifying the main taxon_names table.
 * 
 * Example code to generate a hybrid formula in the frontend:
 * 
 * ```typescript
 * // resources/js/Utils/TaxonNameHelper.ts
 *
 * interface TaxonName {
 *     name_string: string;
 *     name_type: 'SCIENTIFIC' | 'VERNACULAR' | 'HYBRID_FORMULA' | 'HORT_GROUP' | string;
 *     hybrid_formula?: {
 *         first_parent_name: TaxonName;
 *         second_parent_name: TaxonName;
 *     };
 * }
 * 
 * export const generateHybridFormula = (taxon: TaxonName): string => {
 *     // Base Case: Not a hybrid, return the standard name string
 *     if (taxon.name_type !== 'HYBRID_FORMULA' || !taxon.hybrid_formula) {
 *         return taxon.name_string;
 *     }
 * 
 *     const { first_parent_name, second_parent_name } = taxon.hybrid_formula;
 * 
 *     // Recursive calls for parents
 *     let p1 = generateHybridFormula(first_parent_name);
 *     let p2 = generateHybridFormula(second_parent_name);
 * 
 *     // Wrap in parentheses if the parent is itself a hybrid formula
 *     if (first_parent_name.name_type === 'HYBRID_FORMULA') {
 *         p1 = `(${p1})`;
 *     }
 * 
 *     if (second_parent_name.name_type === 'HYBRID_FORMULA') {
 *         p2 = `(${p2})`;
 *     }
 * 
 *     // Using the proper multiplication sign '×'
 *     return `${p1} × ${p2}`;
 * }
 * ```
 * 
 * ```typescript
 * // in component:
 * import { generateHybridFormula } from '@/Utils/TaxonNameHelper';
 * 
 * const TaxonHeader = ({ taxon }) => {
 *     const displayName = taxon.name_type === 'HYBRID_FORMULA' 
 *         ? generateHybridFormula(taxon) 
 *         : taxon.name_string;
 * 
 *     return (
 *         <h1 className="italic text-xl font-semibold">
 *             {displayName}
 *         </h1>
 *     );
 * };
 * ```
 *
 * @property int $id
 * @property int $first_hybrid_parent_name_id
 * @property int $second_hybrid_parent_name_id
 *
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read TaxonName $firstParentName
 * @property-read TaxonName $secondParentName
 */
#[Table(
    name: 'hybrid_formulas_ext', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'first_hybrid_parent_name_id',
    'second_hybrid_parent_name_id',
    'created_by_id',
    'updated_by_id',
])]
class HybridFormula extends Model
{
    use IsSidecar;

    /**
     * Get sidecar fields
     * 
     * Used in IsSidecar trait
     *
     * @return array
     */
    public function getSidecarFields(): array
    {
        return [];
    }

    /**
     * Get the first parent of the hybrid formula.
     *
     * @return BelongsTo
     */
    public function firstParentName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'first_hybrid_parent_name_id');
    }

    /**
     * Get the second parent of the hybrid formula.
     *
     * @return BelongsTo
     */
    public function secondParentName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'second_hybrid_parent_name_id');
    }
}