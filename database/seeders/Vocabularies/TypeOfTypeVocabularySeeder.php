<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeOfTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'TYPE_OF_TYPE'],
            [
                'name' => 'Type of Type Vocabulary',
                'description' => 'Types of type that can be assigned to taxon names.',
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'TYPE_OF_TYPE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'ALLOLECTOTYPE', 
                'label' => 'Allolectotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/allolectotype'
            ],
            [
                'code' => 'ALLONEOTYPE', 
                'label' => 'Alloneotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/alloneotype'
            ],
            [
                'code' => 'ALLOTYPE', 
                'label' => 'Allotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/allotype'
            ],
            [
                'code' => 'COTYPE', 
                'label' => 'Cotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/cotype'
            ],
            [
                'code' => 'EPITYPE', 
                'label' => 'Epitype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/epitype'
            ],
            [
                'code' => 'EXEPITYPE', 
                'label' => 'Exepitype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/exepitype'
            ],
            [
                'code' => 'EXHOLOTYPE', 
                'label' => 'Exholotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/exholotype'
            ],
            [
                'code' => 'EXISOTYPE', 
                'label' => 'Exisotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/exisotype'
            ],
            [
                'code' => 'EXLECTOTYPE', 
                'label' => 'Exlectotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/exlectotype'
            ],
            [
                'code' => 'EXNEOTYPE', 
                'label' => 'Exneotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/exneotype'
            ],
            [
                'code' => 'EXPARATYPE', 
                'label' => 'Exparatype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/exparatype'
            ],
            [
                'code' => 'EXSYNTYPE', 
                'label' => 'Exsyntype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/exsyntype'
            ],
            [
                'code' => 'EXTYPE', 
                'label' => 'Extype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/extype'
            ],
            [
                'code' => 'HAPANTOTYPE', 
                'label' => 'Hapantotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/hapantotype'
            ],
            [
                'code' => 'HOLOTYPE', 
                'label' => 'Holotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/holotype'
            ],
            [
                'code' => 'ISOLECTOTYPE', 
                'label' => 'Isolectotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/isolectotype'
            ],
            [
                'code' => 'ISONEOTYPE', 
                'label' => 'Isoneotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/isoneotype'
            ],
            [
                'code' => 'ISOPARATYPE', 
                'label' => 'Isoparatype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/isoparatype'
            ],
            [
                'code' => 'ISOSYNTYPE', 
                'label' => 'Isosyntype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/isosyntype'
            ],
            [
                'code' => 'ISOTYPE', 
                'label' => 'Isotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/isotype'
            ],
            [
                'code' => 'LECTOTYPE', 
                'label' => 'Lectotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/lectotype'
            ],
            [
                'code' => 'NEOTYPE', 
                'label' => 'Neotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/neotype'
            ],
            [
                'code' => 'PARANEOTYPE', 
                'label' => 'Paraneotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/paraneotype'
            ],
            [
                'code' => 'PARATYPE', 
                'label' => 'Paratype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/paratype'
            ],
            [
                'code' => 'PLASTOHOLOTYPE', 
                'label' => 'Plastoholotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/plastoholotype'
            ],
            [
                'code' => 'PLASTOISOTYPE', 
                'label' => 'Plastoisotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/plastoisotype'
            ],
            [
                'code' => 'PLASTOLECTOTYPE', 
                'label' => 'Plastolectotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/plastolectotype'
            ],
            [
                'code' => 'PLASTONEOTYPE', 
                'label' => 'Plastoneotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/plastoneotype'
            ],
            [
                'code' => 'PLASTOPARATYPE', 
                'label' => 'Plastoparatype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/plastoparatype'
            ],
            [
                'code' => 'PLASTOSYNTYPE', 
                'label' => 'Plastosyntype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/plastosyntype'
            ],
            [
                'code' => 'PLASTOTYPE', 
                'label' => 'Plastotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/plastotype'
            ],
            [
                'code' => 'SECONDARY_TYPE', 
                'label' => 'Secondary type', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/secondarytype'
            ],
            [
                'code' => 'SUPPLEMENTARY_TYPE', 
                'label' => 'Supplementary type', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/supplementarytype'
            ],
            [
                'code' => 'SYNTYPE', 
                'label' => 'Syntype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/syntype'
            ],
            [
                'code' => 'TOPOTYPE', 
                'label' => 'Topotype', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/topotype'
            ],
            [
                'code' => 'TYPE', 
                'label' => 'Type', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/type'
            ],
            [
                'code' => 'TYPE_SPECIES', 
                'label' => 'Type species', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/typeSpecies'
            ],
            [
                'code' => 'TYPE_GENUS', 
                'label' => 'Type genus', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/type_status/typeGenus'
            ]
        ];

        // 3. Insert the terms
        foreach ($terms as $index => $term) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $term['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $term['label'],
                    'description' => null,
                    'sort_order' => $index + 1,
                    'iri' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
