<?php

namespace App\Models\Glossary;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Table;

/**
 * Class Glossary
 *
 * Represents a glossary, which is a collection of terms and their definitions. 
 * This model is based on the 'glossaries' database table, which captures the 
 * metadata about glossaries.
 *
 * The model includes traits for tracking who created and updated the glossary, 
 * as well as versioning.
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $version
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[Table(name: 'glossaries', schema: 'glossary', incrementing: true)]
class Glossary extends Model
{
    use Blameable, IncrementsVersion;
}