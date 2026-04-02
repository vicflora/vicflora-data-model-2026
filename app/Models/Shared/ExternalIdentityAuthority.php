<?php

namespace App\Models\Shared;

use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

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