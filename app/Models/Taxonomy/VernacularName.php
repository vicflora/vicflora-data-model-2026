<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\HasSidecar;
use App\Models\Traits\HasUsages;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

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
