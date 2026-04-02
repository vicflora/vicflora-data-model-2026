<?php

namespace App\Models\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;

#[Table(name: 'mapper.taxa', primaryKey: 'id', incrementing: false)]
#[WithoutTimestamps]
class Taxon extends Model
{
    protected $casts = [
        'scientific_name_id' => 'integer',
        'taxon_concept_id' => 'string',
        'accepted_name_usage_id' => 'string',
        'species_id' => 'string',
    ];

    /**
     * Prevent accidental writes to the Materialized View.
     */
    public function save(array $options = []): bool
    {
        throw new \Exception("Cannot write to a Materialized View.");
    }
}