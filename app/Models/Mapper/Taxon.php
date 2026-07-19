<?php

namespace App\Models\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;

/**
 * Class Taxon
 *
 * Represents a taxonomic concept (taxon) as defined in the 'mapper.taxa'
 * Materialized View. This model captures the taxonomic information for
 * occurrences, including the scientific name, taxonomic hierarchy, and related
 * identifiers.
 *
 * The model is read-only and does not allow for saving or updating records, as
 * it is based on a Materialized View that is refreshed from the underlying
 * taxonomic data.
 *
 * @property-read int $id
 * @property-read string|null $guid
 * @property-read string|null $scientific_name
 * @property-read int|null $scientific_name_id
 * @property-read string|null $taxon_concept_id
 * @property-read string|null $accepted_name_usage_id
 * @property-read string|null $species_id
 * @property-read string|null $family
 * @property-read string|null $genus
 * @property-read string|null $specific_epithet
 * @property-read string|null $infraspecific_epithet
 * @property-read string|null $rank
 * @property-read string|null $authorship
 * @property-read string|null $published_in_string
 * @property-read string|null $microreference
 * @property-read string|null $year
 * @property-read int|null $published_in_id
 * @property-read int|null $nomenclatural_code_id
 * @property-read int|null $nomenclatural_status_id
 */
#[Table(name: 'mapper.taxa', key: 'id', incrementing: false)]
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