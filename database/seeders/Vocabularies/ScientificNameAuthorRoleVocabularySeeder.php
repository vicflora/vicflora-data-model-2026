<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScientificNameAuthorRoleVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'SCIENTIFIC_NAME_AUTHOR_ROLE'],
            [
                'name' => 'Scientific Name Author Role Vocabulary',
                'description' => 'Role of an author in the authorship of a scientific name.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'SCIENTIFIC_NAME_AUTHOR_ROLE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'COMBINATION_AUTHOR', 
                'label' => 'Combination author', 
                'description' => 'Author of the combination.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/combinationAuthor'
            ],
           [
                'code' => 'BASIONYM_AUTHOR', 
                'label' => 'Basionym author', 
                'description' => 'Author of the basionym of the described name.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/basionymAuthor'
            ],
           [
                'code' => 'COMBINATION_ASCIBED_AUTHOR', 
                'label' => 'Combination ascribed author', 
                'description' => 'Ascribed author of the combination.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/combinationAscribedAuthor'
            ],
           [
                'code' => 'BASIONYM_ASCRIBED_AUTHOR', 
                'label' => 'Basionym ascribed author', 
                'description' => 'Ascribed author of the basionym of the described name.', 
                'iri' => 'http://rs.tdwg.org/tcs/terms/basionymAscribedAuthor'
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
