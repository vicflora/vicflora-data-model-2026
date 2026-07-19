<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NameRelationTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'NAME_RELATION_TYPE'],
            [
                'name' => 'Name Relation Type Vocabulary',
                'description' => 'Types of relationships that can exist between names.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'NAME_RELATION_TYPE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'BASIONYM', 
                'label' => 'Basionym', 
                'description' => 'Epithet- or name-bringing synonym.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/basionym'
            ],
            [
                'code' => 'REPLACED_NAME', 
                'label' => 'Replaced Name', 
                'description' => 'The legitimate or illegitimate, previously published name on which a replacement name (nomen novum) is based.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/replacedName'
            ],
            [
                'code' => 'BASED_ON', 
                'label' => 'Based on', 
                'description' => 'Earlier name on which this name is based, but which is not a basionym or replaced name.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/basedOn'
            ],
            [
                'code' => 'LATER_HOMONYM_OF', 
                'label' => 'Later Homonym of', 
                'description' => 'An older legitimate name with the same spelling but a different nomenclatural type.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/laterHomonymOf'
            ],
            [
                'code' => 'CONSERVED_AGAINST', 
                'label' => 'Conserved Against', 
                'description' => 'Name(s) against which this name is conserved.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/conservedAgainst'
            ],
            [
                'code' => 'ORTHOGRAPHIC_VARIANT_OF', 
                'label' => 'Orthographic Variant of', 
                'description' => 'Invalid name with different spelling but same nomenclatural type as valid name.',
                'iri' => null
            ],
            [
                'code' => 'AMBIREGNAL',
                'label' => 'Ambiregnal',
                'description' => 'Name under a different nomenclatural code that applies to the same taxon.',
                'iri' => null
            ]
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
