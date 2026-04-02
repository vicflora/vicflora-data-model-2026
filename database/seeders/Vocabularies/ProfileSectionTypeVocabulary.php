<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSectionTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'PROFILE_SECTION_TYPE'],
            [
                'name' => 'Profile Section Type Vocabulary',
                'description' => 'Types of profile sections.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'PROFILE_SECTION_TYPE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'DESCRIPTION', 
                'label' => 'Description', 
                'iri' => null,
            ],
            [
                'code' => 'STATE_DISTRIBUTION', 
                'label' => 'State Distribution', 
                'iri' => null,
            ],
            [
                'code' => 'WORLD_DISTRIBUTION', 
                'label' => 'World Distribution', 
                'iri' => null,
            ],
            [
                'code' => 'HABITAT', 
                'label' => 'Habitat', 
                'iri' => null,
            ],
            [
                'code' => 'PHENOLOGY', 
                'label' => 'Phenology', 
                'iri' => null,
            ],
            [
                'code' => 'NOTES', 
                'label' => 'Notes', 
                'iri' => null,
            ],
            [
                'code' => 'REFERENCES', 
                'label' => 'References', 
                'iri' => null,
            ],
        ];

        // 3. Insert the terms
        foreach ($terms as $index => $term) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $term['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $term['label'],
                    'description' => null,
                    'sort_order' => $index + 1,
                    'iri' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
