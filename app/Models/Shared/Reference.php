<?php

namespace App\Models\Shared;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    use Blameable;

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