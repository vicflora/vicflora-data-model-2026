<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageRoleVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'IMAGE_ROLE'],
            [
                'name' => 'Image Role Vocabulary',
                'description' => 'Roles that an image can have in relation to a profile.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );      

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'IMAGE_ROLE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'HERO', 
                'label' => 'Hero Image', 
                'iri' => null,
            ],
            [
                'code' => 'GALLERY', 
                'label' => 'Gallery Image', 
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