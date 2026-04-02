<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlossaryTermRelationshipTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'GLOSSARY_TERM_RELATIONSHIP_TYPE'],
            [
                'name' => 'Glossary Term Relationship Type Vocabulary',
                'description' => 'Types of relationships between glossary terms.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'GLOSSARY_TERM_RELATIONSHIP_TYPE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'HAS_SYNONYM', 
                'label' => 'Has Synonym', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_EXACT_SYNONYM', 
                'label' => 'Has Exact Synonym', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_MORE_INCLUSIVE_SYNONYM', 
                'label' => 'Has More Inclusive Synonym', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_LESS_INCLUSIVE_SYNONYM', 
                'label' => 'Has Less Inclusive Synonym', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_PARTIALLY_OVERLAPPING_SYNONYM', 
                'label' => 'Has Partially Overlapping Synonym', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_APPROXIMATELY_EQUAL_SYNONYM', 
                'label' => 'Has Approximately Equal Synonym', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_PLURAL', 
                'label' => 'Has Plural', 
                'iri' => null,
            ],
            [
                'code' => 'IS_PLURAL_OF', 
                'label' => 'Is Plural Of', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_VARIATION', 
                'label' => 'Has Variation', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_ADJECTIVE', 
                'label' => 'Has Adjective', 
                'iri' => null,
            ],
            [
                'code' => 'IS_RELATED_TO_CF', 
                'label' => 'Is Related To (cf.)', 
                'iri' => null,
            ],
            [
                'code' => 'IS_VARIATION_OF', 
                'label' => 'Is Variation Of', 
                'iri' => null,
            ],
            [
                'code' => 'IS_ADJECTIVE_OF', 
                'label' => 'Is Adjective Of', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_ABBREVIATION', 
                'label' => 'Has Abbreviation', 
                'iri' => null,
            ],
            [
                'code' => 'IS_ABBREVIATION_OF', 
                'label' => 'Is Abbreviation Of', 
                'iri' => null,
            ],
            [
                'code' => 'HAS_SINGULAR', 
                'label' => 'Has Singular', 
                'iri' => null,
            ],
            [
                'code' => 'IS_SINGULAR_OF', 
                'label' => 'Is Singular Of', 
                'iri' => null,
            ],
            [
                'code' => 'IS_RELATED_TO_SEE', 
                'label' => "Is Related To (see)", 
                'iri' => null,
            ],
            [
                'code' => "IS_OPPOSED_TO", 
                'label' => "Is Opposed To", 
                "iri" => null,
            ],
            [
                "code" => "HAS_TRANSLATION", 
                "label" => "Has Translation", 
                "iri" => null,
            ],
            [
                "code" => "IS_TRANSLATION_OF", 
                "label" => "Is Translation Of",
                "iri" => null,
            ],
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
