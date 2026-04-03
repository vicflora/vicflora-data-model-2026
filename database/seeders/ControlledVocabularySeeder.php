<?php

namespace Database\Seeders;

use Database\Seeders\Vocabularies\AgentTypeVocabularySeeder;
use Database\Seeders\Vocabularies\AreaTypeVocabularySeeder;
use Database\Seeders\Vocabularies\ChangeTypeVocabularySeeder;
use Database\Seeders\Vocabularies\DegreeOfEstablishmentVocabularySeeder;
use Database\Seeders\Vocabularies\EstablishmentMeansVocabularySeeder;
use Database\Seeders\Vocabularies\GlossaryTermRelationshipTypeVocabularySeeder;
use Database\Seeders\Vocabularies\ImageRoleVocabularySeeder;
use Database\Seeders\Vocabularies\ImageTypeVocabularySeeder;
use Database\Seeders\Vocabularies\ImageVariantVocabularySeeder;
use Database\Seeders\Vocabularies\MappingMethodVocabularySeeder;
use Database\Seeders\Vocabularies\MappingRelationVocabularySeeder;
use Database\Seeders\Vocabularies\NameRelationTypeVocabularySeeder;
use Database\Seeders\Vocabularies\NameUsageRoleVocabularySeeder;
use Database\Seeders\Vocabularies\NomenclaturalCodeVocabularySeeder;
use Database\Seeders\Vocabularies\NomenclaturalStatusVocabularySeeder;
use Database\Seeders\Vocabularies\OccurrenceStatusVocabularySeeder;
use Database\Seeders\Vocabularies\ProfileSectionTypeVocabularySeeder;
use Database\Seeders\Vocabularies\ReferenceTypeVocabularySeeder;
use Database\Seeders\Vocabularies\TaxonConceptComponentVocabularySeeder;
use Database\Seeders\Vocabularies\TaxonRankVocabularySeeder;
use Database\Seeders\Vocabularies\ThreatStatusVocabularySeeder;
use Database\Seeders\Vocabularies\TypeOfTypeVocabularySeeder;
use Database\Seeders\Vocabularies\VoucherTypeVocabularySeeder;
use Illuminate\Database\Seeder;

class ControlledVocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AgentTypeVocabularySeeder::class,
            ReferenceTypeVocabularySeeder::class,
            TaxonRankVocabularySeeder::class,
            MappingRelationVocabularySeeder::class,
            TaxonConceptComponentVocabularySeeder::class,
            MappingMethodVocabularySeeder::class,
            NomenclaturalCodeVocabularySeeder::class,
            NomenclaturalStatusVocabularySeeder::class,
            NameRelationTypeVocabularySeeder::class,
            NameUsageRoleVocabularySeeder::class,
            TypeOfTypeVocabularySeeder::class,
            ChangeTypeVocabularySeeder::class,
            ProfileSectionTypeVocabularySeeder::class,
            OccurrenceStatusVocabularySeeder::class,
            EstablishmentMeansVocabularySeeder::class,
            DegreeOfEstablishmentVocabularySeeder::class,
            ThreatStatusVocabularySeeder::class,
            VoucherTypeVocabularySeeder::class,
            ImageRoleVocabularySeeder::class,
            ImageTypeVocabularySeeder::class,
            ImageVariantVocabularySeeder::class,
            GlossaryTermRelationshipTypeVocabularySeeder::class,
            AreaTypeVocabularySeeder::class,
        ]);
    }
}
