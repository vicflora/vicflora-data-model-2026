# VicFlora data model 2026

[Full documentation](./docs)

```
/
├── app/
│   ├── Models/
│   │   ├── Mapper/
│   │   │   ├── Assertion.php
│   │   │   ├── MapOverlay.php
│   │   │   ├── NameMatchMap.php
│   │   │   ├── Occurrence.php
│   │   │   ├── ParsedName.php
│   │   │   ├── Taxon.php
│   │   │   ├── TaxonConceptMapOverlayMap.php
│   │   │   ├── TaxonConceptOccurrenceMap.php
│   │   │   └── TaxonConceptPhenologyMap.php
│   │   ├── Profile/
│   │   │   ├── Gazetteer.php
│   │   │   ├── Image.php
│   │   │   ├── ImageAccessPoint.php
│   │   │   ├── ImageCaption.php
│   │   │   ├── Profile.php
│   │   │   ├── ProfileAreaMap.php
│   │   │   ├── ProfileDefItem.php
│   │   │   ├── ProfileImageMap.php
│   │   │   ├── ProfileSection.php
│   │   │   ├── ProfileSpecimenMap.php
│   │   │   ├── Specimen.php
│   │   │   ├── SpecimenImageMap.php
│   │   │   └── ThreatStatusAuthority.php
│   │   ├── Shared/
│   │   │   ├── Agent.php
│   │   │   ├── AuditLog.php
│   │   │   ├── ControlledTerm.php 
│   │   │   ├── ControlledVocabular.php 
│   │   │   ├── ControlledTerm.php
│   │   │   ├── EntityIdentityMap.php
│   │   │   ├── ExternalIdentity.php
│   │   │   ├── ExternalIdentityAuthorit.php
│   │   │   ├── Reference.php
│   │   │   └── User.php
│   │   ├── Search/
│   │   │   └── Search.php
│   │   ├── Taxonomy/
│   │   │   ├── NameRelationMap.php
│   │   │   ├── NomenclaturalType.php
│   │   │   ├── Protologue.php
│   │   │   ├── ScientificName.php
│   │   │   ├── TaxonConcept.php
│   │   │   ├── TaxonConceptMapping.php
│   │   │   ├── TaxonName.php
│   │   │   ├── TaxonNameUsageMap.php
│   │   │   ├── Taxonomy.php
│   │   │   ├── TaxonomyVersion.php
│   │   │   ├── TaxonTree.php
│   │   │   ├── TaxonTreeDefItem.php
│   │   │   ├── TaxonTreeGeographicScope.php
│   │   │   ├── TaxonTreeNode.php
│   │   │   ├── TaxonTreeRevision.php
│   │   │   ├── TraditionaKnowledgeLabel.php
│   │   │   ├── Treatment.php
│   │   │   ├── TreatmentVersion.php
│   │   │   └── VernacularName.php
│   │   └── Traits/
│   │       ├── Blameable.php
│   │       ├── HasSidecar.php
│   │       ├── HasUsage.php
│   │       └── IncrementsVersion.php
│   └── Observers/
│       ├── ImageCaptionObserver.php
│       ├── ImageObserver.php
│       ├── ProfileImageMapObserver.php
│       ├── ProfileObserver.php
│       ├── TaxonConceptObserver.php
│       └── TaxonTreeRevisionObserver.php
└── database/
    ├── migrations/
    │   ├── 0001_01_01_000000_create_users_table.php
    │   ├── 0001_01_01_000001_create_cache_table.php
    │   ├── 0001_01_01_000002_create_jobs_table.php
    │   ├── 2026_01_01_000001_create_agents_table.php
    │   ├── 2026_01_01_000002_create_controlled_vocabularies_table.php
    │   ├── 2026_01_01_000003_create_controlled_terms_table.php
    │   ├── 2026_01_01_000004_add_foreign_keys_to_agents_table.php
    │   ├── 2026_01_01_000005_create_audit_logs_table.php
    │   ├── 2026_03_28_000006_create_references_table.php
    │   ├── 2026_03_28_000007_create_taxonomies_table.php
    │   ├── 2026_03_28_000008_create_taxonomy_versions_ext_table.php
    │   ├── 2026_03_28_000009_create_treatments_ext_table.php
    │   ├── 2026_03_28_000010_create_treatment_versions_ext_table.php
    │   ├── 2026_03_28_000011_create_protologues_ext_table.php
    │   ├── 2026_03_28_000012_create_gazetteers_ext_table.php
    │   ├── 2026_03_28_000013_create_threat_status_authorities_ext_table.php
    │   ├── 2026_03_28_000014_create_external_identity_authorities_ext_table.php
    │   ├── 2026_03_28_000015_create_reference_views.php
    │   ├── 2026_03_28_000016_create_taxon_names_table.php
    │   ├── 2026_03_28_000017_create_scientific_names_ext_table.php
    │   ├── 2026_03_28_000018_create_vernacular_names_ext_table.php
    │   ├── 2026_03_28_000019_create_traditional_knowledge_labels_ext_table.php
    │   ├── 2026_03_28_000020_create_taxon_name_views.php
    │   ├── 2026_03_28_000021_create_name_relations_map_table.php
    │   ├── 2026_03_28_000022_create_taxon_trees_table.php
    │   ├── 2026_03_28_000023_create_taxon_tree_geographic_scope_map_table.php
    │   ├── 2026_03_28_000024_create_taxon_concepts_table.php
    │   ├── 2026_03_28_000025_create_taxon_name_usages_map_table.php
    │   ├── 2026_03_28_000026_create_taxon_concept_mappings_table.php
    │   ├── 2026_03_28_000027_create_specimens_table.php
    │   ├── 2026_03_28_000028_create_nomenclatural_types_table.php
    │   ├── 2026_03_28_000029_create_taxon_tree_def_items_table.php
    │   ├── 2026_03_28_000030_create_taxon_tree_nodes_table.php
    │   ├── 2026_03_28_000031_create_taxon_tree_revisions_table.php
    │   ├── 2026_03_28_000032_create_external_identities_table.php
    │   ├── 2026_03_28_000033_create_entity_identity_map_table.php
    │   ├── 2026_03_28_000034_create_profile_def_items_table.php
    │   ├── 2026_03_28_000035_create_profiles_table.php
    │   ├── 2026_03_28_000036_create_profile_sections_table.php
    │   ├── 2026_03_29_000037_create_profile_specimen_map_table.php
    │   ├── 2026_03_29_000038_create_profile_area_map_table.php
    │   ├── 2026_03_29_000039_create_images_table.php
    │   ├── 2026_03_29_000040_create_image_access_points_table.php
    │   ├── 2026_03_29_000041_create_image_captions_table.php
    │   ├── 2026_03_29_000042_create_profile_image_map_table.php
    │   ├── 2026_03_29_000043_create_specimen_image_map_table.php
    │   ├── 2026_03_30_000044_enable_postgis_extension.php
    │   ├── 2026_03_30_000045_create_assertions_table.php
    │   ├── 2026_03_30_000046_create_mapper_schema.php
    │   ├── 2026_03_30_000047_create_taxa_materialized_view.php
    │   ├── 2026_03_30_000048_create_occurrences_table.php
    │   ├── 2026_03_30_000049_create_parsed_names_table.php
    │   ├── 2026_03_30_000050_create_name_matches_map_materialized_view.php
    │   ├── 2026_03_30_000051_create_taxon_concept_occurrence_map_materialized_view.php
    │   ├── 2026_03_30_000052_create_map_overlays_table.php
    │   ├── 2026_03_30_000053_create_taxon_concept_map_overlay_map_materialized_view.php
    │   ├── 2026_03_30_000054_create_taxon_concept_phenology_map.php
    │   └── 2026_03_31_000055_create_search_materialized_view.php
    └── seeders/
        ├── ControlledVocabularySeeder.php
        ├── DatabaseSeeder.php
        └── Vocabularies/
            ├── AgentTypeVocabularySeeder.php
            ├── ChangeTypeVocabularySeeder.php
            ├── DegreeOfEstablishmentVocabularySeeder.php
            ├── EstablishmentMeansVocabularySeeder.php
            ├── ImageRoleVocabularySeeder.php
            ├── ImageTypeVocabularySeeder.php
            ├── ImageVariantVocabularySeeder.php
            ├── MappingMethodVocabularySeeder.php
            ├── MappingRelationVocabularySeeder.php
            ├── NameRelationTypeVocabularySeeder.php
            ├── NameUsageRoleVocabularySeeder.php
            ├── NomenclaturalCodeVocabularySeeder.php
            ├── NomenclaturalStatusVocabularySeeder.php
            ├── OccurrenceStatusVocabularySeeder.php
            ├── ReferenceTypeVocabularySeeder.php
            ├── TaxonConceptComponentVocabularySeeder.php
            ├── TaxonRankVocabularySeeder.php
            ├── ThreatStatusVocabularySeeder.php
            ├── TypeOfTypeVocabularySeeder.php
            └── VoucherTypeVocabularySeeder.php
```