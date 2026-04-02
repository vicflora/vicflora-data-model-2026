<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MappingMethodVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'MAPPING_METHOD'],
            [
                'name' => 'Mapping Method Vocabulary',
                'description' => 'Methods that can be used to map taxon concepts.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'MAPPING_METHOD')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'ASSERTED', 
                'label' => 'Asserted', 
                'description' => 'A mapping based on an assertion.', 
                'iri' => null
            ],
            [
                'code' => 'INFERRED', 
                'label' => 'Inferred', 
                'description' => 'A mapping based on inference.', 
                'iri' => null
            ],
            [
                'code' => 'INFERRED_REVERSE', 
                'label' => 'Inferred (Reverse)', 
                'description' => 'A reverse mapping.', 
                'iri' => null
            ],
            [
                'code' => 'INFERRED_CONFIRMED', 
                'label' => 'Inferred (Confirmed)', 
                'description' => 'A confirmed inferred mapping.',
                'iri' => null
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
