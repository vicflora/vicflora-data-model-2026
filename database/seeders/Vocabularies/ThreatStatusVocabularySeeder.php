<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ThreatStatusVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'THREAT_STATUS'],
            [
                'name' => 'Threat Status Vocabulary',
                'description' => 'Darwin Core Threat Status Vocabulary.',
                'iri' => 'http://rs.tdwg.org/dwcts/values/',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'THREAT_STATUS')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'EX', 
                'label' => 'Extinct', 
                'description' => 'A taxon is Extinct when there is no reasonable doubt that the last individual has died.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/EX'
            ],
            [
                'code' => 'EW', 
                'label' => 'Extinct in the wild', 
                'description' => 'A taxon is Extinct in the Wild when it is known only to survive in cultivation, in captivity or as a naturalized population well outside the past range.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/EW'
            ],
            [
                'code' => 'RE', 
                'label' => 'Regionally extinct', 
                'description' => 'Category for a taxon when there is no reasonable doubt that the last individual potentially capable of reproduction within the region has died or has disappeared from the wild in the region.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/RE'
            ],
            [
                'code' => 'CR', 
                'label' => 'Critically endangered', 
                'description' => 'A taxon is Critically Endangered when the best available evidence indicates that it meets any of the criteria A to E for Critically Endangered.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/CR'
            ],
            [
                'code' => 'EN', 
                'label' => 'Endangered', 
                'description' => 'A taxon is Endangered when the best available evidence indicates that it meets any of the criteria A to E for Endangered.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/EN'
            ],
            [
                'code' => 'VU', 
                'label' => 'Vulnerable', 
                'description' => 'A taxon is Vulnerable when the best available evidence indicates that it meets any of the criteria A to E for Vulnerable.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/VU'
            ],
            [
                'code' => 'NT', 
                'label' => 'Near threatened', 
                'description' => 'A taxon is Near Threatened when it has been evaluated against the criteria but does not qualify for Critically Endangered, Endangered or Vulnerable now, but is close to qualifying for or is likely to qualify for a threatened category in the near future.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/NT'
            ],
            [
                'code' => 'LC', 
                'label' => 'Least concern', 
                'description' => 'A taxon is Least Concern when it has been evaluated against the criteria and does not qualify for Critically Endangered, Endangered, Vulnerable or Near Threatened.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/LC'
            ],
            [
                'code' => 'DD', 
                'label' => 'Data deficient', 
                'description' => 'A taxon is Data Deficient when there is inadequate information to make a direct, or indirect, assessment of its risk of extinction based on its distribution and/or population status.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/DD'
            ],
            [
                'code' => 'NA', 
                'label' => 'Not applicable', 
                'description' => 'Category for a taxon deemed to be ineligible for assessment at a regional level.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/NA'
            ],
            [
                'code' => 'NE', 
                'label' => 'Not evaluated', 
                'description' => 'A taxon is Not Evaluated when it is has not yet been evaluated against the criteria.',
                'iri' => 'http://rs.gbif.org/vocabulary/iucn/threat_status/NE'
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