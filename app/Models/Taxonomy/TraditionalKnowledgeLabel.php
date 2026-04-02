<?php

namespace App\Models\Taxonomy;

use App\Models\Traits\HasSidecar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(
    name: 'traditional_knowledge_labels', 
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
class TraditionalKnowledgeLabel extends Model
{
    use HasSidecar;
    
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
        return 'traditional_knowledge_labels_ext';
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
