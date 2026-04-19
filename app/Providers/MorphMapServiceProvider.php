<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphMapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            // Geography
            'area' => \App\Models\Geography\Area::class,
            'area_code' => \App\Models\Geography\AreaCode::class,
            'gazetteer' => \App\Models\Geography\Gazetteer::class,

            // Glossary
            'category' => \App\Models\Glossary\Category::class,
            'glossary' => \App\Models\Glossary\Glossary::class,
            'limitation' => \App\Models\Glossary\Limitation::class,
            'term' => \App\Models\Glossary\Term::class,
            'term_relationship' => \App\Models\Glossary\TermRelationship::class,

            // Image
            'image' => \App\Models\Media\Image::class,
            'image_access_point' => \App\Models\Media\ImageAccessPoint::class,
            'image_caption' => \App\Models\Media\ImageCaption::class,

            // Mapper
            'assertion' => \App\Models\Mapper\Assertion::class,
            'map_overlay' => \App\Models\Mapper\MapOverlay::class,
            'name_match_map' => \App\Models\Mapper\NameMatchMap::class,
            'occurrence' => \App\Models\Mapper\Occurrence::class,
            'parsed_name' => \App\Models\Mapper\ParsedName::class,
            'taxon' => \App\Models\Mapper\Taxon::class,
            'taxon_concept_map_overlay_map' => \App\Models\Mapper\TaxonConceptMapOverlayMap::class,
            'taxon_concept_occurrence_map' => \App\Models\Mapper\TaxonConceptOccurrenceMap::class,

            // Profile
            'profile' => \App\Models\Profile\Profile::class,
            'profile_area_map' => \App\Models\Profile\ProfileAreaMap::class,
            'profile_section' => \App\Models\Profile\ProfileSection::class,
        ]);
    }
}
