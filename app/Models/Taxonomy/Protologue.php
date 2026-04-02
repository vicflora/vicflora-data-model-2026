<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(
    name: 'protologues', 
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
    'microreference',
])]
class Protologue extends Model
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
        return 'protologues_ext';
    }

    public function getSidecarFields(): array
    {
        return ['microreference'];
    }
}