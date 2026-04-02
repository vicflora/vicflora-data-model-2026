<?php

namespace App\Models\Glossary;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Blameable;
use App\Models\Traits\IncrementsVersion;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table(name: 'glossaries', schema: 'glossary', incrementing: true)]
class Glossary extends Model
{
    use Blameable, IncrementsVersion;
}