<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoucherTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'VOUCHER_TYPE'],
            [
                'name' => 'Voucher Type Vocabulary',
                'description' => 'Types of voucher that can be linked to profiles.',
                'iri' => null,
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
                'code' => 'CITED', 
                'label' => 'Cited Specimen', 
                'iri' => null,
            ],
            [
                'code' => 'REPRESENTATIVE', 
                'label' => 'Representative Specimen', 
                'iri' => null,
            ],
            [
                'code' => 'VOUCHER', 
                'label' => 'Voucher Specimen', 
                'iri' => null,
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
