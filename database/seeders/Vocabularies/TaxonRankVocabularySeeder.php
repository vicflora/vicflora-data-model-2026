<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxonRankVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'TAXON_RANK'],
            [
                'name' => 'Taxon Rank Vocabulary',
                'description' => 'Ranks used in the taxonomic classification of organisms.',
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'TAXON_RANK')
            ->value('id');

        // 2. Define the terms
        $ranks = [
            [
                'code' => 'DOMAIN', 
                'label' => 'Domain', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/domain'
            ],
            [
                'code' => 'KINGDOM', 
                'label' => 'Kingdom', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/kingdom'
            ],
            [
                'code' => 'SUBKINGDOM', 
                'label' => 'Subkingdom', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subkingdom'
            ],
            [
                'code' => 'SUPERPHYLUM', 
                'label' => 'Superphylum', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/superphylum'
            ],
            [
                'code' => 'PHYLUM', 
                'label' => 'Phylum', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/phylum'
            ],
            [
                'code' => 'SUBPHYLUM', 
                'label' => 'Subphylum', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subphylum'
            ],
            [
                'code' => 'SUPERCLASS', 
                'label' => 'Superclass', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/superclass'
            ],
            [
                'code' => 'CLASS', 
                'label' => 'Class', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/class'
            ],
            [
                'code' => 'SUBCLASS', 
                'label' => 'Subclass', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subclass'
            ],
            [
                'code' => 'SUPERCOHORT', 
                'label' => 'Supercohort', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/supercohort'
            ],
            [
                'code' => 'COHORT', 
                'label' => 'Cohort', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/cohort'
            ],
            [
                'code' => 'SUBCOHORT', 
                'label' => 'Subcohort', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subcohort'
            ],
            [
                'code' => 'SUPERORDER', 
                'label' => 'Superorder', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/superorder'
            ],
            [
                'code' => 'ORDER', 
                'label' => 'Order', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/order'
            ],
            [
                'code' => 'SUBORDER', 
                'label' => 'Suborder', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/suborder'
            ],
            [
                'code' => 'INFRAORDER', 
                'label' => 'Infraorder', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/infraorder'
            ],
            [
                'code' => 'SUPERFAMILY', 
                'label' => 'Superfamily', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/superfamily'
            ],
            [
                'code' => 'FAMILY', 
                'label' => 'Family', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/family'
            ],
            [
                'code' => 'SUBFAMILY', 
                'label' => 'Subfamily', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subfamily'
            ],
            [
                'code' => 'TRIBE', 
                'label' => 'Tribe', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/tribe'
            ],
            [
                'code' => 'SUBTRIBE', 
                'label' => 'Subtribe', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subtribe'
            ],
            [
                'code' => 'GENUS', 
                'label' => 'Genus', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/genus'
            ],
            [
                'code' => 'SUBGENUS', 
                'label' => 'Subgenus', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subgenus'
            ],
            [
                'code' => 'SECTION', 
                'label' => 'Section', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/section'
            ],
            [
                'code' => 'SUBSECTION', 
                'label' => 'Subsection', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subsection'
            ],
            [
                'code' => 'SERIES', 
                'label' => 'Series', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/series'
            ],
            [
                'code' => 'SUBSERIES', 
                'label' => 'Subseries', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subseries'
            ],
            [
                'code' => 'SPECIESAGGREGATE', 
                'label' => 'Speciesaggregate', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/speciesAggregate'
            ],
            [
                'code' => 'SPECIES', 
                'label' => 'Species', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/species'
            ],
            [
                'code' => 'SUBSPECIFICAGGREGATE', 
                'label' => 'Subspecificaggregate', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subspecificAggregate'
            ],
            [
                'code' => 'SUBSPECIES', 
                'label' => 'Subspecies', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subspecies'
            ],
            [
                'code' => 'VARIETY', 
                'label' => 'Variety', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/variety'
            ],
            [
                'code' => 'SUBVARIETY', 
                'label' => 'Subvariety', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subvariety'
            ],
            [
                'code' => 'FORM', 
                'label' => 'Form', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/form'
            ],
            [
                'code' => 'SUBFORM', 
                'label' => 'Subform', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/subform'
            ],
            [
                'code' => 'PATHOVAR', 
                'label' => 'Pathovar', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/pathovar'
            ],
            [
                'code' => 'BIOVAR', 
                'label' => 'Biovar', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/biovar'
            ],
            [
                'code' => 'CHEMOVAR', 
                'label' => 'Chemovar', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/chemovar'
            ],
            [
                'code' => 'MORPHOVAR', 
                'label' => 'Morphovar', 
                'iri' => 
                'http://rs.gbif.org/vocabulary/gbif/rank/morphovar'
            ],
            [
                'code' => 'PHAGOVAR', 
                'label' => 'Phagovar', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/phagovar'
            ],
            [
                'code' => 'SEROVAR', 
                'label' => 'Serovar', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/serovar'
            ],
            [
                'code' => 'CHEMOFORM', 
                'label' => 'Chemoform', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/chemoform'
            ],
            [
                'code' => 'FORMASPECIALIS', 
                'label' => 'Formaspecialis', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/formaspecialis'
            ],
            [
                'code' => 'CULTIVARGROUP', 
                'label' => 'Cultivargroup', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/cultivarGroup'
            ],
            [
                'code' => 'CULTIVAR', 
                'label' => 'Cultivar', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/cultivar'
            ],
            [
                'code' => 'STRAIN', 
                'label' => 'Strain', 
                'iri' => 'http://rs.gbif.org/vocabulary/gbif/rank/strain'
            ],
        ];

        // 3. Insert the terms
        foreach ($ranks as $index => $rank) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $rank['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $rank['label'],
                    'sort_order' => $index + 1,
                    'iri' => $rank['iri'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
