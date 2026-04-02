<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class OccurrenceStatusVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'OCCURRENCE_STATUS'],
            [
                'name' => 'Occurrence Status Vocabulary',
                'description' => 'Statuses that can be used to describe the occurrence of taxa.',
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/occurrence_status/',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'OCCURRENCE_STATUS')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'PRESENT', 
                'label' => 'Present', 
                'description' => 'The taxon is present in the area.', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/occurrence_status/present'
            ],
            [
                'code' => 'ABSENT', 
                'label' => 'Extinct', 
                'description' => 'The taxon is extinct in the area.', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/occurrence_status/absent'
            ],
            [
                'code' => 'DOUBTFUL', 
                'label' => 'Doubtful', 
                'description' => 'The occurrence of the taxon in the area is doubtful.', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/occurrence_status/doubtful'
            ],
            [
                'code' => 'EXCLUDED', 
                'label' => 'Excluded', 
                'description' => 'The taxon has been erroneously reported from the area.', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/occurrence_status/excluded'
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
