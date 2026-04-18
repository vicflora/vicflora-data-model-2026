<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EstablishmentMeansVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'ESTABLISHMENT_MEANS'],
            [
                'name' => 'Establishment Means Vocabulary',
                'description' => 'Darwin Core Establishment Means Vocabulary.',
                'iri' => 'http://rs.tdwg.org/dwcem/values/',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'ESTABLISHMENT_MEANS')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'NATIVE', 
                'label' => 'Native', 
                'description' => 'A taxon occurring within its natural range.',
                'iri' => 'http://rs.tdwg.org/dwcem/values/e001'
            ],
            [
                'code' => 'NATIVE_REINTRODUCED', 
                'label' => 'Native: reintroduced', 
                'description' => 'A taxon re-established by direct introduction by humans into an area that was once part of its natural range, but from where it had become extinct.',
                'iri' => 'http://rs.tdwg.org/dwcem/values/e002'
            ],
            [
                'code' => 'INTRODUCED', 
                'label' => 'Introduced', 
                'description' => 'Establishment of a taxon by human agency into an area that is not part of its natural range.',
                'iri' => 'http://rs.tdwg.org/dwcem/values/e003'
            ],
            [
                'code' => 'INTRODUCED_ASSISTED_COLONISATION', 
                'label' => 'Introduced: assisted colonisation', 
                'description' => "Establishment of a taxon specifically with the intention of creating a self-sustaining wild population in an area that is not part of the taxon's natural range.",
                'iri' => 'http://rs.tdwg.org/dwcem/values/e004'
            ],
            [
                'code' => 'VAGRANT', 
                'label' => 'Vagrant', 
                'description' => 'The temporary occurrence of a taxon far outside its natural or migratory range.',
                'iri' => 'http://rs.tdwg.org/dwcem/values/e005'
            ],
            [
                'code' => 'UNCERTAIN', 
                'label' => 'Uncertain', 
                'description' => 'The origin of the occurrence of the taxon in an area is obscure.',
                'iri' => 'http://rs.tdwg.org/dwcem/values/e006'
            ],
            [
                'code' => 'NATIVE_ENDEMIC', 
                'label' => 'Native: endemic', 
                'description' => 'A taxon with a natural distribution restricted to a single geographical area.',
                'iri' => 'http://rs.tdwg.org/dwcem/values/e007'
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
