<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContributorRoleVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'CONTRIBUTOR_ROLE'],
            [
                'name' => 'Contributor Role Vocabulary',
                'description' => 'Roles that contributors to a Reference might have.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'CONTRIBUTOR_ROLE')
            ->value('id');

        // 2. Define the terms
        $roles = [
            [
                'code' => 'AUTHOR', 
                'label' => 'Author', 
                'description' => null
            ],
            [
                'code' => 'EDITOR', 
                'label' => 'Editor', 
                'description' => null
            ],
        ];

        // 3. Insert the terms
        foreach ($roles as $index => $role) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $role['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $role['label'],
                    'description' => $role['description'],
                    'sort_order' => $index + 1,
                    'iri' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

    }
}
