<?php

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Reference
 *
 * Represents a reference, which is a bibliographic citation that may be associated with various entities in the application. This model is based on the 'references_view' database view, which aggregates data from multiple tables to provide a comprehensive representation of references and their roles.
 *
 * The model includes relationships to the reference type (ControlledTerm) and provides helper methods to check for specific roles and types based on the aggregated data from the view.
 * 
 * @property int $id
 * @property int|null $reference_type_id
 * @property string|null $author_string
 * @property string|null $year
 * @property string|null $title
 * @property string|null $doi
 * @property string|null $uri
 * @property array|null $metadata
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read ControlledTerm|null $type
 * @property-read \App\Models\Shared\Agent|null $createdBy
 * @property-read \App\Models\Shared\Agent|null $updatedBy
 */
#[Table(
    name: 'references_view', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'reference_type_id',
    'author_string',
    'year',
    'title',
    'doi',
    'uri',
    'metadata',
])]
class Reference extends Model
{
    use Blameable, IncrementsVersion;

    /**
     * Relationship to the Vocabulary Layer (Layer 8)
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'reference_type_id');
    }

    /**
     * Helper: Check if the reference has a specific role.
     * This parses the aggregated string from the CASE/COALESCE logic in the view.
     */
    public function hasRole(string $role): bool
    {
        if (!$this->reference_roles) {
            return false;
        }

        $roles = explode(', ', $this->reference_roles);
        return in_array(strtoupper($role), $roles);
    }

    /**
     * Accessor: Get roles as a clean array for the React/Inertia frontend.
     */
    public function getRoleListAttribute(): array
    {
        return $this->reference_roles 
            ? explode(', ', $this->reference_roles) 
            : ['GENERAL'];
    }


    /**
     * Check if the name has a specific functional extension type.
     * * @param string $type e.g., 'SCIENTIFIC', 'VERNACULAR'
     * @return bool
     */
    public function hasType(string $type): bool
    {
        // Since the view provides 'name_type', we check it directly.
        // We uppercase both to ensure the check is robust.
        return strtoupper($this->name_type) === strtoupper($type);
    }




}