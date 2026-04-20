<?php

namespace App\Models\Taxonomy;

use App\Models\Shared\Agent;
use App\Models\Profile\Specimen;
use App\Models\Shared\ControlledTerm;
use App\Models\Shared\Reference;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use App\Models\Traits\Sourceable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class NomenclaturalType
 *
 * Represents a nomenclatural type, which is a specific kind of relationship 
 * between taxonomic names and their types. This model is based on the 
 * 'nomenclatural_types' database table, which captures the typification of 
 * taxonomic names.
 *
 * The model includes relationships to the typified name (TaxonName), the type 
 * of type (ControlledTerm), the type name (TaxonName), the type specimen 
 * (Specimen), the reference in which the name was typified (Reference), and the 
 * reference in which a specimen was designated as the type (Reference).
 * 
 * @property int $id
 * @property int $typified_name_id
 * @property int $type_of_type_id
 * @property int|null $type_name_id
 * @property int|null $type_specimen_id
 * @property int|null $type_published_in_id
 * @property int|null $source_id
 * @property string|null $remarks
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read TaxonName $typifiedName
 * @property-read ControlledTerm $typeOfType
 * @property-read TaxonName|null $typeName
 * @property-read Specimen|null $typeSpecimen
 * @property-read Reference|null $typePublishedIn
 * @property-read Reference|null $source
 * @property-read Agent $createdBy
 * @property-read Agent $updatedBy
 */
#[Table(
    name: 'nomenclatural_types', 
    key: 'id', 
    incrementing: true
)]
#[Fillable([
    'id',
    'created_at',
    'updated_at',
    'typified_name_id',
    'type_of_type_id',
    'type_name_id',
    'type_specimen_id',
    'type_published_in_id',
    'source_id',
    'remarks',
    'created_by_id',
    'updated_by_id',
])]
class NomenclaturalType extends Model
{
    use Blameable, IncrementsVersion, Sourceable;

    /**
     * Define the relationship to the typified name (TaxonName).
     * This is the name that is being typified by this nomenclatural type.
     * 
     * @return BelongsTo
     */
    public function typifiedName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'typified_name_id');
    }

    /**
     * Define the relationship to the type of type (ControlledTerm).
     * We filter the related ControlledTerm to only those in the 'TYPE_OF_TYPE' 
     * vocabulary.
     * 
     * @return BelongsTo
     */
    public function typeOfType(): BelongsTo
    {
        return $this->belongsTo(ControlledTerm::class, 'type_of_type_id')
            ->whereHas('vocabulary', function ($query) {
                $query->where('code', 'TYPE_OF_TYPE');
            });
    }

    /**
     * The Name that serves as the nomenclatural type.
     *
     * @return BelongsTo
     */
    public function typeName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'type_name_id');
    }

    /**
     * The Specimen that serves as the nomenclatural type.
     * 
     * @return BelongsTo
     */
    public function typeSpecimen(): BelongsTo
    {
        return $this->belongsTo(Specimen::class, 'type_specimen_id');
    }

    /**
     * The Reference in which the name was typified. In most cases this will be 
     * the same as the Reference in which the type name was published, but it 
     * for some types of type, e.g. lectotypes and conserved types, the 
     * typification is changed later.
     * 
     * @return BelongsTo
     */
    public function typePublishedIn(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'type_published_in_id');
    }

}
