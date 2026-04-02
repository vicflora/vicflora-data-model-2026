<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MappingRelationVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'MAPPING_RELATION'],
            [
                'name' => 'Mapping Relation Vocabulary',
                'description' => 'Relations that can be used to map taxon concepts.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'MAPPING_RELATION')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'IS_CONGRUENT_WITH', 
                'label' => 'Is Congruent With', 
                'description' => 'The subject and object taxon concepts have a congruent taxonomic meaning, i.e. there is no conflict between the concepts.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/isCongruentWith'
            ],
            [
                'code' => 'INCLUDES', 
                'label' => 'Includes', 
                'description' => 'The subject taxon concept has a more inclusive taxonomic meaning than the object taxon concept.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/includes'
            ],
            [
                'code' => 'IS_INCLUDED_IN', 
                'label' => 'Is Included In', 
                'description' => 'The subject taxon concept has a less inclusive taxonomic meaning than the object taxon concept.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/isIncludedIn'
            ],
            [
                'code' => 'PARTIALLY_OVERLAPS', 
                'label' => 'Partially Overlaps', 
                'description' => 'The subject and object taxon concepts have partially overlapping taxonomic meanings, i.e. they have some members in common, but each concept in addition has members that are not included in the other concept.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/partiallyOverlaps'
            ],
            [
                'code' => 'IS_DISJOINT_FROM', 
                'label' => 'Is Disjoint From', 
                'description' => 'The subject and object taxon concepts have no overlapping taxonomic meanings, i.e. they have no members in common.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/isDisjointFrom'
            ],
            [
                'code' => 'INTERSECTS', 
                'label' => 'Intersects', 
                'description' => 'The taxonomic meanings of the subject and object taxon concepts intersect, i.e. they have at least one member in common.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/intersects'
            ],
        ];

        // 3. Insert the terms
        foreach ($terms as $index => $term) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $term['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $term['label'],
                    'description' => $term['description'],
                    'sort_order' => $index + 1,
                    'iri' => $term['iri'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
