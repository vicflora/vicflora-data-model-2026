<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TaxonConceptComponentVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'TAXON_CONCEPT_COMPONENT'],
            [
                'name' => 'Taxon Concept Component Vocabulary',
                'description' => 'Components of the taxon concepts on which a mapping is based.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'TAXON_CONCEPT_COMPONENT')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'INTENSIONAL', 
                'label' => 'Intensional', 
                'description' => 'The intensional component of a taxon concept.', 
                'iri' => null
            ],
            [
                'code' => 'OSTENSIVE', 
                'label' => 'Ostensive', 
                'description' => 'The ostensive component of a taxon concept.', 
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
