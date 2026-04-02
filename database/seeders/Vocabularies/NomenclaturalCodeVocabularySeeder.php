<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NomenclaturalCodeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'NOMENCLATURAL_CODE'],
            [
                'name' => 'Nomenclatural Code Vocabulary',
                'description' => 'GBIF recommended terms for denoting a nomenclatural code.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'NOMENCLATURAL_CODE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'ICN', 
                'label' => 'International Code of Nomenclature for algae, fungi and plants',
                'description' => null,
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_code/ICN'
            ],
            [
                'code' => 'ICZN', 
                'label' => 'International Code of Zoological Nomenclature', 
                'description' => null,
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_code/ICZN'
            ],
            [
                'code' => 'ICVCN', 
                'label' => 'International Code of Virus Classifications and Nomenclature', 
                'description' => null,
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_code/ICVCN'
            ],
            [
                'code' => 'ICNB', 
                'label' => 'International Code of Nomenclature of Bacteria', 
                'description' => null,
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_code/ICNB'
            ],
            [
                'code' => 'ICNCP', 
                'label' => 'International Code of Nomenclature for Cultivated Plants', 
                'description' => null,
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_code/ICNCP'
            ],
            [
                'code' => 'BIOCODE', 
                'label' => 'International Code of Botanical Nomenclature', 
                'description' => null,
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/nomenclatural_code/BioCode'
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
