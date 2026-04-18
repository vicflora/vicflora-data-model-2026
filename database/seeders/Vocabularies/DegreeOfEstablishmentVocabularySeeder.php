<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DegreeOfEstablishmentVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'DEGREE_OF_ESTABLISHMENT'],
            [
                'name' => 'Degree of Establishment Vocabulary',
                'description' => 'Darwin Core Degree of Establishment Vocabulary.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'DEGREE_OF_ESTABLISHMENT')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'NATIVE', 
                'label' => 'Native', 
                'description' => 'Not transported beyond limits of native range.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d001'
            ],
            [
                'code' => 'CAPTIVE', 
                'label' => 'Captive', 
                'description' => 'Individuals in captivity or quarantine (i.e., individuals provided with conditions suitable for them, but explicit measures of containment are in place).',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d002'
            ],
            [
                'code' => 'CULTIVATED', 
                'label' => 'Cultivated', 
                'description' => 'Individuals in cultivation (i.e., individuals provided with conditions suitable for them, but explicit measures to prevent dispersal are limited at best).',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d003'
            ],
            [
                'code' => 'RELEASED', 
                'label' => 'Released', 
                'description' => 'Individuals directly released into novel environment.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d004'
            ],
            [
                'code' => 'FAILING', 
                'label' => 'Failing', 
                'description' => 'Individuals released outside of captivity or cultivation in a location, but incapable of surviving for a significant period.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d005'
            ],
            [
                'code' => 'CASUAL', 
                'label' => 'Casual', 
                'description' => 'Individuals surviving outside of captivity or cultivation in a location with no reproduction.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d006'
            ],
            [
                'code' => 'REPRODUCING', 
                'label' => 'Adventive', 
                'description' => 'Individuals surviving outside of captivity or cultivation in a location. Reproduction is occurring, but population not self-sustaining.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d007'
            ],
            [
                'code' => 'ESTABLISHED', 
                'label' => 'Naturalised', 
                'description' => 'Individuals surviving outside of captivity or cultivation in a location. Reproduction occurring, and population self-sustaining.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d008'
            ],
            [
                'code' => 'COLONISING', 
                'label' => 'Colonising', 
                'description' => 'Self-sustaining population outside of captivity or cultivation, with individuals surviving a significant distance from the original point of introduction.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d009'
            ],
            [
                'code' => 'INVASIVE', 
                'label' => 'Invasive', 
                'description' => 'Self-sustaining population outside of captivity or cultivation, with individuals surviving and reproducing a significant distance from the original point of introduction.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d010'
            ],
            [
                'code' => 'WIDESPREAD_INVASIVE', 
                'label' => 'Widespread invasive', 
                'description' => 'Fully invasive species, with individuals dispersing, surviving and reproducing at multiple sites across a spectrum of habitats and geographic range.',
                'iri' => 'http://rs.tdwg.org/dwcdoe/values/d011'
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
