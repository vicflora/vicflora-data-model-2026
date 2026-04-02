<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgentTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'AGENT_TYPE'],
            [
                'name' => 'Agent Type Vocabulary',
                'description' => 'Types of agents that can interact with the system.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'AGENT_TYPE')
            ->value('id');

        // 2. Define the terms
        $agentTypes = [
            [
                'label' => 'Person', 
                'code' => 'PERSON', 
                'description' => 'An individual human being.'
            ],
            [
                'label' => 'Group', 
                'code' => 'GROUP', 
                'description' => 'A collection of individuals.'
            ],
            [
                'label' => 'Organization', 
                'code' => 'ORGANIZATION', 
                'description' => 'A named group or institution.'
            ],
            [
                'label' => 'Software', 
                'code' => 'SOFTWARE', 
                'description' => 'A software agent or bot.'
            ],
        ];

        // 3. Insert the terms
        foreach ($agentTypes as $index =>$agentType) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $agentType['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $agentType['label'],
                    'description' => $agentType['description'],
                    'sort_order' => $index + 1,
                    'iri' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
