<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NomenclaturalStatusVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'NOMENCLATURAL_STATUS'],
            [
                'name' => 'Nomenclatural Status Vocabulary',
                'description' => 'Statuses that can be assigned to taxon names.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'NOMENCLATURAL_STATUS')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'LEGITIMATE', 
                'label' => 'Legitimate', 
                'description' => 'A name that is considered legitimate under the rules of nomenclature.', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_status/legitimate'
            ],
            [
                'code' => 'ILLEGITIMATE', 
                'label' => 'Illegitimate', 
                'description' => 'A name that is considered illegitimate under the rules of nomenclature.', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_status/illegitimum'
            ],
            [
                'code' => 'SUPERFLUOUS', 
                'label' => 'Superfluous', 
                'description' => 'A name that is considered superfluous under the rules of nomenclature.', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_status/superfluum'
            ],
            [
                'code' => 'INVALID', 
                'label' => 'Invalid', 
                'description' => 'A name that is considered invalid under the rules of nomenclature.', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_status/invalidum'
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
