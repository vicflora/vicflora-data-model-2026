<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'IMAGE_TYPE'],
            [
                'name' => 'Image Type Vocabulary',
                'description' => 'Types of images that can be uploaded.',
                'iri' => 'http://rs.tdwg.org/acsubtype/values/',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );      

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'IMAGE_TYPE')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'PHOTOGRAPH', 
                'label' => 'Photograph', 
                'iri' => 'http://rs.tdwg.org/acsubtype/values/Photograph',
                'description' => 'Refers to still images produced from radiation-sensitive materials (sensitive to light, electron beams, or nuclear radiation), generally by means of the chemical action of light on a sensitive film, paper, glass, or metal. Photographs may be positive or negative, opaque or transparent. The concept may include photographs made by digital means.',
            ],
            [
                'code' => 'ILLUSTRATION', 
                'label' => 'Illustration', 
                'iri' => 'http://rs.tdwg.org/acsubtype/values/Illustration',
                'description' => 'Pictures or diagrams that clarify or provide an example or visualization. They usually accompany a text; the term is most often used to refer to pictures in books or published journal.',
            ],
        ];

        // 3. Insert the terms
        foreach ($terms as $index => $term) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $term['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $term['label'],
                    'description' => $term['description'] ?? null,
                    'sort_order' => $index + 1,
                    'iri' => $term['iri'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}