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
            'occurrence' => \App\Models\Mapper\Occurrence::class,
            'parsed_name' => \App\Models\Mapper\ParsedName::class,

            // Profile
            'profile' => \App\Models\Profile\Profile::class,
            'profile_section' => \App\Models\Profile\ProfileSection::class,


            // Shared
            'agent' => \App\Models\Shared\Agent::class,
            'external_identity' => \App\Models\Shared\ExternalIdentity::class,
            'reference' => \App\Models\Shared\Reference::class,

            // Taxonomy
            'nomenclatural_type' => \App\Models\Taxonomy\NomenclaturalType::class,
            'taxon_concept' => \App\Models\Taxonomy\TaxonConcept::class,
            'taxon_concept_mapping' => \App\Models\Taxonomy\TaxonConceptMapping::class,
            'taxon_tree' => \App\Models\Taxonomy\TaxonTree::class,
            'taxon_tree_node' => \App\Models\Taxonomy\TaxonTreeNode::class,
            'taxon_tree_revision' => \App\Models\Taxonomy\TaxonTreeRevision::class,            
        ]);
    }
}
