<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageVariantVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'IMAGE_VARIANT'],
            [
                'name' => 'Image Variant Vocabulary',
                'description' => 'Variants of images that can be uploaded.',
                'iri' => 'http://rs.tdwg.org/acsubtype/values/',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );      

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'IMAGE_VARIANT')
            ->value('id');

        // 2. Define the terms
        $terms = [
            [
                'code' => 'THUMBNAIL', 
                'label' => 'Thumbnail', 
                'iri' => 'http://rs.tdwg.org/acvariant/values/v001',
                'description' => 'Service Access Point provides a thumbnail image, short sound clip, or short movie clip that can be used in addition to the resource to represent the media object, typically at lower quality and higher compression than a preview object.',
            ],
            [
                'code' => 'PREVIEW', 
                'label' => 'Preview', 
                'iri' => 'http://rs.tdwg.org/acvariant/values/v004',
                'description' => 'Shortened in duration, reduced size, using lower resolution or higher compression causing moderate artifacts.',
            ],
            [
                'code' => 'HIGHESTRES', 
                'label' => 'Highest Resolution', 
                'iri' => 'http://rs.tdwg.org/acvariant/values/v006',
                'description' => 'Service Access Point provides the highest available quality of the media resource, whatever its resolution or quality level.',
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