<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NameUsageRoleVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'NAME_USAGE_ROLE'],
            [
                'name' => 'Name Usage Role Vocabulary',
                'description' => 'Roles that names can have in the taxonomy.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'NAME_USAGE_ROLE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'ACCEPTED', 
                'label' => 'Accepted', 
                'description' => 'The primary or correct name for a taxon.'
            ],
            [
                'code' => 'SYNONYM', 
                'label' => 'Synonym', 
                'description' => 'A name that is considered equivalent to the accepted name.'
            ],
            [
                'code' => 'VERNACULAR_NAME', 
                'label' => 'Vernacular Name', 
                'description' => 'A common or local name for a taxon.'
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
                    'iri' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
