<?php

namespace App\Models\Shared;

use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExternalIdentityAuthority
 *
 * Represents an external identity authority, which is a reference that can be
 * used to validate external identities. This model is based on the
 * 'external_identity_authorities' database view, which combines data from the
 * 'references' table and its related extension for external identity
 * authorities.
 *
 * The model includes relationships to the base Reference model and any sidecar
 * data.
 *
 * @property int $id
 * @property int $reference_type_id
 * @property string $author_string
 * @property int|null $year
 * @property string|null $title
 * @property string|null $doi
 * @property string|null $url
 * @property array|null $metadata
 */
#[Table(
    name: 'external_identity_authorities', 
    key: 'id', 
    incrementing: false
)]
#[Fillable([
    'id',
    'reference_type_id',
    'author_string',
    'year',
    'title',
    'doi',
    'url',
    'metadata',
])]
class ExternalIdentityAuthority extends Model
{
    use HasSidecar;

    protected $casts = [
        'metadata' => 'array',
    ];

    public function getBaseTable(): string
    {
        return 'references';
    }

    public function getBaseModelClass(): string
    {
        return Reference::class;
    }
    
    public function getExtensionTable(): string
    {
        return 'external_identity_authorities_ext';
    }

    public function getSidecarFields(): array
    {
        return [];
    }
}