<?php

namespace Database\Seeders\Vocabularies;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceTypeVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the parent Vocabulary exists and get its ID
        // We use updateOrInsert to ensure the 'code' is the unique anchor
        DB::table('controlled_vocabularies')->updateOrInsert(
            ['code' => 'REFERENCE_TYPE'],
            [
                'name' => 'Reference Type Vocabulary',
                'description' => 'Types of references that can be associated with records.',
                'iri' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $vocabId = DB::table('controlled_vocabularies')
            ->where('code', 'REFERENCE_TYPE')
            ->value('id');    

        // 2. Define the terms
        $referenceTypes = [
            [
                'label' => 'Bibliographic Resource', 
                'code' => 'BIBLIOGRAPHIC_RESOURCE', 
                'description' => 'A book, article, or other documentary resource.', 
                'iri' => 'http://purl.org/dc/terms/BibliographicResource'
            ],
            [
                'label' => 'Academic Article', 
                'code' => 'ACADEMIC_ARTICLE', 
                'description' => 'A scholarly academic article, typically published in a journal.', 
                'iri' => 'http://purl.org/ontology/bibo/AcademicArticle'
            ],
            [
                'label' => 'Book', 
                'code' => 'BOOK', 
                'description' => 'A written or printed work of fiction or nonfiction, usually on sheets of paper fastened or bound together within covers.', 
                'iri' => 'http://purl.org/ontology/bibo/Book'
            ],
            [
                'label' => 'EditedBook', 
                'code' => 'EDITED_BOOK', 
                'description' => 'A book that has been edited by one or more editors.', 
                'iri' => 'http://purl.org/ontology/bibo/EditedBook'
            ],
            [
                'label' => 'MultiVolumeBook', 
                'code' => 'MULTI_VOLUME_BOOK', 
                'description' => 'A loose, thematic, collection of Documents, often Books.', 
                'iri' => 'http://purl.org/ontology/bibo/MultiVolumeBook'
            ],
            [
                'label' => 'BookSection', 
                'code' => 'BOOK_SECTION', 
                'description' => 'A section of a book.', 
                'iri' => 'http://purl.org/ontology/bibo/BookSection'
            ],
            [
                'label' => 'Chapter', 
                'code' => 'CHAPTER', 
                'description' => 'A chapter of a book.', 
                'iri' => 'http://purl.org/ontology/bibo/Chapter'
            ],
            [
                'label' => 'Dataset', 
                'code' => 'DATASET', 
                'description' => 'A collection of data, typically organized in a structured format.', 
                'iri' => 'http://purl.org/dc/terms/Dataset'
            ],
            [
                'label' => 'Journal', 
                'code' => 'JOURNAL', 
                'description' => 'A periodical publication containing articles and other items.', 
                'iri' => 'http://purl.org/ontology/bibo/Journal'
            ],
            [
                'label' => 'Report', 
                'code' => 'REPORT', 
                'description' => 'A document that presents information in an organized format for a specific audience and purpose.', 
                'iri' => 'http://purl.org/ontology/bibo/Report'
            ],
            [
                'label' => 'Standard', 
                'code' => 'STANDARD', 
                'description' => 'A document describing a standard: a specification organized through a standards body.', 
                'iri' => 'http://purl.org/ontology/bibo/Standard'
            ],
            [
                'label' => 'Thesis', 
                'code' => 'THESIS', 
                'description' => "A document submitted in support of candidature for an academic degree or professional qualification presenting the author's research and findings.", 
                'iri' => 'http://purl.org/ontology/bibo/Thesis'
            ],
            [
                'label' => 'Webpage', 
                'code' => 'WEB_PAGE', 
                'description' => 'A web page is an online document available (at least initially) on the world wide web. A web page is written first and foremost to appear on the web, as distinct from other online resources such as books, manuscripts or audio documents which use the web primarily as a distribution mechanism alongside other more traditional methods such as print.', 
                'iri' => 'http://purl.org/ontology/bibo/Webpage'
            ],  
            [
                'label' => 'Website', 
                'code' => 'WEBSITE', 
                'description' => 'A group of Webpages accessible on the Web.', 
                'iri' => 'http://purl.org/ontology/bibo/Website'
            ],  
            [
                'label' => 'Manuscript', 
                'code' => 'MANUSCRIPT', 
                'description' => 'An unpublished Document, which may also be submitted to a publisher for publication.', 
                'iri' => 'http://purl.org/ontology/bibo/Manuscript'
            ], 
            [
                'label' => 'Proceedings', 
                'code' => 'PROCEEDINGS', 
                'description' => 'A compilation of documents published from an event, such as a conference..', 
                'iri' => 'http://purl.org/ontology/bibo/Proceedings'
            ], 
            [
                'label' => 'PersonalCommunication', 
                'code' => 'PERSONAL_COMMUNICATION', 
                'description' => 'A communication between an agent and one or more specific recipients.', 
                'iri' => 'http://purl.org/ontology/bibo/PersonalCommunication'
            ],
            [
                'label' => 'Treatment', 
                'code' => 'TREATMENT', 
                'description' => 'Placeholder for a treatment, until we think of a more appropriate type.', 
                'iri' => null
            ]
        ];

        // 3. Insert the terms
        foreach ($referenceTypes as $index => $referenceType) {
            DB::table('controlled_terms')->updateOrInsert(
                ['code' => $referenceType['code'], 'controlled_vocabulary_id' => $vocabId],
                [
                    'label' => $referenceType['label'],
                    'description' => $referenceType['description'],
                    'iri' => $referenceType['iri'],
                    'sort_order' => $index + 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
