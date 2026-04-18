<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'CHANGE_TYPE'],
            [
                'name' => 'Change Type Vocabulary',
                'description' => 'Types of changes that can be made to the taxonomy.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'CHANGE_TYPE')
            ->value('id');

        // 2. Define the terms
        $changeTypes = [
            [
                'code' => 'MERGE', 
                'label' => 'Merge', 
                'description' => 'Merging two or more taxa into one.'],
            [
                'code' => 'SPLIT', 
                'label' => 'Split', 
                'description' => 'Splitting a taxon into two or more taxa.'
            ],
            [
                'code' => 'TRANSFER', 
                'label' => 'Transfer', 
                'description' => 'Changing the classification of a taxon without changing its circumscription.'
            ],
            [
                'code' => 'NEW', 
                'label' => 'New', 
                'description' => 'Adding a new taxon to the taxonomy.'
            ],
            [
                'code' => 'REMOVE', 
                'label' => 'Remove', 
                'description' => 'Removing a taxon from the taxonomy.'
            ],
            [
                'code' => 'OTHER', 
                'label' => 'Other', 
                'description' => 'Other type of change.'
            ],
        ];

        // 3. Insert the terms
        foreach ($changeTypes as $index => $changeType) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $changeType['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $changeType['label'],
                    'description' => $changeType['description'],
                    'sort_order' => $index + 1,
                    'iri' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

    }
}
