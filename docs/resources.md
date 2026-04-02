# VicFlora Data Model: Resources

## 1. Domain Entities

### Agent

**Table name:** agents \
**Layer:** [7a. Agency and provenance](layers.md#layer-7a-agency-and-provenance)

Represents the "Who" of the system. This includes individual researchers,
organizations, or automated software agents. It provides a unified way to handle
attribution, ORCIDs, and ownership across all data layers.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `int` | No | **Primary Key**. |
| **agent_type_id** | `int` | No | **FK to ControlledTerm** (e.g., Person, Organization). |
| **name** | `string` | No | Full name of the agent. |
| **initials** | `string` | No | Initials for short-form attribution. |
| **orcid** | `string` | Yes | Open Researcher and Contributor ID. |
| **uri** | `string` | Yes | Persistent URI for the agent. |

---

### ExternalIdentity

**Table name:** external_identities \
**Layer:** [6. Identity](layers.md#layer-6-identity)

Stores the literal identifier values and URIs originating from external
authority systems. It acts as the bridge between internal records and the global
biodiversity informatics community.


**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | **Primary Key**. |
| **external_identity_authority_id** | `Int` | No | **FK to ExternalIdentityAuthority**. |
| **system_type_id** | `Int` | No | **FK to ControlledTerm** (e.g., 'LSID', 'UUID', 'URL'). |
| **external_id** | `String` | No | The literal value of the identifier. |
| **external_uri** | `String` | Yes | The fully qualified link. |
| **last_synced_at** | `Timestamp` | Yes | For cache/sync management. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **externalIdentityAuthority** | `BelongsTo` | ExternalIdentityAuthority |

---

### Image

**Table name:** images \
**Layer:** [8c. Media](layers.md#layer-8c-media-extension)

A domain-level repository for media assets. It manages the URI paths to hosted
files alongside critical metadata such as license types, rights holders,
creators, and technical specifications (e.g., dimensions and formats).


| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | **Primary Key**. |
| **image_type_id** | `Int` | No | **FK to ControlledTerm** (e.g., 'PHOTOGRAPH', 'ILLUSTRATION', 'MAP'). |
| **caption** | `Text` | Yes | Context-specific caption (overrides generic image alt-text). |
| **uri** | `String` | No | The resolvable path to the image file (CDN or local storage). |
| **pixel_width** | `Int` | Yes | Metadata for layout engines. |
| **pixel_height** | `Int` | Yes | Metadata for layout engines. |
| **file_size** | `Int` | Yes | Size in bytes. |
| **mime_type** | `String` | Yes | e.g., 'image/jpeg', 'image/png'. |
| **license_id** | `Int` | Yes | **FK to ControlledTerm** (e.g., CC-BY, CC-0). |
| **creator** | `String` | Yes | The photographer or illustrator. |
| **rights_holder** | `String` | Yes | Verbatim copyright statement. |

---

### ProfileSection

**Table name:** profile_sections \
**Layer:** [5. Narrative](layers.md#layer-5-narrative-description)

A modular narrative unit. These segments hold the actual content of a taxonomic account (e.g., Diagnosis, Etymology, Habitat). By breaking narratives into sections, the system allows for granular updates and structured data exchange.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **profile_id** | `Int` | No | **FK**. Parent profile. |
| **profile_section_type_id** | `Int` | No | **FK to ControlledTerm** (e.g., Diagnosis, Etymology). |
| **source_id** | `Int` | Yes | **FK to Reference**. The authority for this text. |
| **body_text** | `Text` | No | The narrative content (Markdown/HTML). |
| **sort_order** | `Int` | Yes | Display sequence in the UI. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **profile** | `BelongsTo` | Profile |

---

### Reference

**Table name:** references \
**Layer:** [4. Authority](layers.md#layer-4-authority-bibliography)

The evidentiary backbone of the database. Every assertion in the system—from a name's protologue to a concept's circumscription—must link to a Reference, which stores bibliographic metadata, persistent identifiers, and source codes.

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **reference_type_id** | `Int` | No | **FK to ControlledTerm** (Book, Article, etc.). |
| **title** | `String` | No | Title of the work |
| **short_title** | `String` | Yes | Standard citation (e.g., *Flora of Victoria*). |
| **full_citation** | `Text` | No | The complete, formatted bibliographic string. |
| **author_string** | `String` | Yes | Literal string of authors. |
| **year** | `String` | Yes | Publication year. |
| **doi** | `String` | Yes | Digital Object Identifier. |
| **uri** | `String` | Yes | Persistent link to the source. |
| **code** | `String` | Yes | Code for authority, e.g., `APC`, `APNI`, `ISO`, `TDWG-WGS` |
| **metadata** | `JSONB` | Yes | Type-specific fields (Volume, Issue, Page). |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | ---|
| **taxonomyVersion** | `HasOne` | Reference | |
| **treatment** | `HasOne` | Reference | |

**Enums**

| Field | Vocabulary | Key terms |
| --- | --- | --- |
| **reference_type_id** | `ReferenceType` | `JOURNAL_ARTICLE`, `BOOK`, `CHAPTER` |

---

### Specimen

**Table name:** specimens \
**Layer:** [8b. Voucher](layers.md#layer-8b-voucher-extension)

A connector entity for physical or digital voucher data. It stores external GUIDs to museum or herbarium collections, allowing the system to cite physical evidence without needing to manage the full lifecycle of a collection management system.

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | **Primary Key**. |
| **institution_code** | `String` | Yes | The herbarium or museum code (e.g., "MEL"). |
| **catalog_number** | `String` | Yes | Barcode or accession number. |
| **recorded_by** | `String` | Yes | Collector name. |
| **record_number** | `String` | Yes | Collector's series number. |
| **event_date** | `Date` | Yes | When the specimen was collected. |
| **decimal_latitude** | `Double` | Yes | Georeferenced coordinate. |
| **decimal_longitude** | `Double` | Yes | Georeferenced coordinate. |
| **source_url** | `String` | Yes | URL to the specimen record in an external source. | 
| **external_source_id** | `Int` | Yes | **FK to Reference**. The system the record was imported from (e.g., GBIF). |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **externalSource** | `BelongsTo` | Reference |

---

### TaxonConcept

**Table name:** taxon_concepts \
**Layer:** [1a. Concept](layers.md#layer-1a-concept)

The central semantic pillar of the system. It represents a specific taxonomic
circumscription as defined by an authority ("According To"). It decouples
scientific opinion from the name string, allowing the system to handle multiple
competing interpretations of the same organism.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **guid** | `uuid` | Yes | Globally Unique Identifier. |
| **taxon_name_id** | `Int` | No | Links to the **TaxonName** union. Represents the name string used. |
| **according_to_id** | `Int` | No | Links to the **Reference** (Secundum). This pairing defines the concept's identity. |
| **rank_id** | `Int` | Yes | Links to a **ControlledTerm**. The taxonomic rank assigned *by this specific authority*. |
| **remarks** | `String` | Yes | General notes regarding this specific concept circumscription. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxonName** | `BelongsTo` | TaxonName | |
| **accordingTo** | `BelongsTo` | Reference | |
| **rank** | `BelongsTo` | ControlledTerm | |
| **mappings** | `HasMany` | TaxonConceptMapping | |
| **acceptedName** | `HasOneThrough` | TaxonName | TaxonNameUsage_MAP (role=ACCEPTED) | 
| **synonyms** | `HasManyThrough` | TaxonName | TaxonNameUsage_MAP (role=SYNONYM) | 
| **vernacularNames** | `HasManyThrough` | TaxonName | TaxonNameUsage_MAP (role=VERNACULAR) | 
| **preferredVernacularName** | `HasManyThrough` | TaxonName | TaxonNameUsage_MAP (role=VERNACULAR, is_preferred_vernacular_name=TRUE) | 
| **isCongruentWith** | `HasManyThrough` | TaxonConcept | TaxonConceptMapping (relation=IS_CONGRUENT_WITH)
| **includes** | `HasManyThrough` | TaxonConcept | TaxonConceptMapping (relation=INCLUDES)
| **isIncludedIn** | `HasManyThrough` | TaxonConcept | TaxonConceptMapping (relation=IS_INCLUDED_IN)
| **partiallyOverlaps** | `HasManyThrough` | TaxonConcept | TaxonConceptMapping (relation=PARTIALLY_OVERLAPS)
| **isDisjointWith** | `HasManyThrough` | TaxonConcept | TaxonConceptMapping (relation=IS_DISJOINT_WITH)
| **intersects** | `HasManyThrough` | TaxonConcept | TaxonConceptMapping (relation=INTERSECTS)
| **profile** | HasOne | Profile | |

**Enums**

| Field | Vocabulary | Controlled terms |
| --- | --- | --- |
| rank | Rank | `KINGDOM`, `PHYLUM`, `SPECIES`, `...` |

---

### TaxonName

**Table name:** taxon_names \
**Layer:** [2a. Core nomenclature](layers.md#layer-2a-core-nomenclature)

A central registry for all nomenclature strings. It serves as a "String Vault"
for scientific names, vernacular names, and Traditional Knowledge labels,
ensuring that name strings are reused consistently across the ecosystem without
duplication.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **guid** | `uuid` | Yes | Globally Unique Identifier. |
| **full_name** | `String` | No | The complete name string. |
| **language** | `String` | Yes | ISO code (e.g., 'en', 'yiy'). |
| **rank_id** | `Int` | Yes | FK to **ControlledTerm** (Rank). |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| scientificName | HasOne | ScientificName (EXT) | |
| traditionalKnowledgeLabel | HasOne | TraditionalKnowledgeLabel (EXT) | |
| vernacularName | HasOne | VernacularName (EXT) | |
| typification | HasMany | NomenclaturalType | |
| basionym | HasOneThrough | TaxonName | NameRelation_MAP (relation=BASIONYM) |
| replacedName | HasOneThrough | TaxonName | NameRelation_MAP (relation=REPLACED_NAME) |
| basedOn | HasOneThrough | TaxonName | NameRelation_MAP (relation=BASED_ON) |
| conservedAgainst | HasManyThrough | TaxonName | NameRelation_MAP (relation=CONSERVED_AGAINST) |
| rejectedAgainst | HasManyThrough | TaxonName | NameRelation_MAP (relation=CONSERVED_AGAINST (INV)) |

---

### TaxonTree

**Table name:** taxon_trees \
**Layer:** [3. Governance](layers.md#layer-3-governance-classification)

A governance container for a specific taxonomic arrangement. It allows different
tenants to maintain their own hierarchical classifications (e.g., a "VicFlora
Tree" vs. an "APC Tree") using the same underlying concepts.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **name** | `String` | No | Human-readable title (e.g., "APG IV", "VicFlora Main Tree"). |
| **is_published** | `Boolean` | Yes | Global flag for visibility status. |

---

### TaxonTreeRevision

**Table name:** taxon_tree_revisions \
**Layer:** [3. Governance](layers.md#layer-3-governance-classification)

The versioning mechanism for classifications. It records the state of a tree at a specific point in time, allowing the system to track and "replay" taxonomic changes like splits, lumps, and moves.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal PK. |
| **taxon_tree_id** | `Int` | No | The tree being revised. |
| **version_id** | `Int` | Yes | **FK to Reference**. The "TaxonomyVersion" this change belongs to. |
| **old_node_id** | `Int` | Yes | The node as it existed before the change. |
| **new_node_id** | `Int` | Yes | The node as it exists after the change. |
| **change_type_id** | `Int` | No | **FK to ControlledTerm** (e.g., SPLIT, LUMP, MOVE, ADD). |
| **effective_date** | `Date` | No | When the change was officially applied. |
| **remarks** | `Text` | Yes | Reason for the change (e.g., "Based on new molecular data"). |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxonTree** | `BelongsTo` | TaxonTree | |
| **oldNode** | `BelongsTo` | TaxonTreeNode | |
| **newNode** | `BelongsTo` | TaxonTreeNode | |
| **version** | `BelongsTo` | TaxonomyVersion | |

**Enums**

| Field | Vocabulary | Key terms |
| --- | --- | --- |
| **change_type_id** | `ChangeType` | `ADD`, `SPLIT`, `LUMP`, `MOVE`, `DELETE` |

---

## 2. Associative Entities

### NomenclaturalType

**Table name:** nomenclatural_types \
**Layer:** [2c. Typification](layers.md#layer-2c-typification)

The objective anchor for nomenclature. It formalizes the designation of a type
for a name, linking the TaxonName to either a physical Specimen (for
species/subspecies) or another TaxonName (for higher ranks).

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **typified_name_id** | `Int` | No | The name being typified. |
| **type_name_id** | `Int` | Yes | For names based on other names (e.g., type genus). |
| **specimen_id** | `Int` | Yes | FK to **Specimen** (The physical type). |
| **type_of_type_id** | `Int` | No | FK to **ControlledTerm** (e.g., Holotype, Lectotype). |
| **published_in_id** | `Int` | Yes | The reference where the type was designated. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| *typifiedName* | `BelongsTo` | TaxonName | |
| *typeName* | `BelongsTo` | TaxonName | |
| *typeSpecimen* | `BelongsTo` | Specimen | |
| *publishedIn* | `BelongsTo` | reference | |

**Enums**

| Field | Vocabulary | Key Terms |
| --- | --- | --- |
| **name_role_id_** | `NameRole` | `ACCEPTED`, `SYNONYM`, `MISAPPLIED`, `VERNACULAR` |
| **type_of_type_id_** | `TypeOfType` | `HOLOTYPE`, `ISOTYPE`, `LECTOTYPE`, `SYNTYPE` |

---

### TaxonConceptMapping

**Table name:** taxon_concept_mappings \
**Layer:** [1b. Mapping](layers.md#layer-1b-mapping)

The primary tool for taxonomic alignment. It facilitates complex, many-to-many
relationships between disparate TaxonConcepts, defining semantic relationships
such as "is congruent with", "overlaps" and "is included in"

**Fields**

| Field | Type | Nullable | Description & Business Rules |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **guid** | `uuid` | Yes | Globally Unique Identifier. |
| **subject_taxon_concept_id** | `Int` | No | The concept being mapped (the "from" side). |
| **object_taxon_concept_id** | `Int` | No | The concept being mapped to (the "to" side). |
| **mapping_relation_id** | `Int` | No | Links to **ControlledTerm** (e.g., `is congruent to`, `includes`, `overlaps with`). |
| **taxon_concept_component_id** | `Int` | Yes | Links to **ControlledTerm**. Specifies if mapping is restricted (e.g., `Distribution only`). |
| **mapping_method_id** | `Int` | Yes | Links to **ControlledTerm**. Defines how mapping was derived (e.g., `Expert Opinion`, `Algorithmic`). |
| **source_id** | `Int` | Yes | Links to **Reference**. The publication or agent asserting this relationship. |
| **creator_id** | `Int` | Yes | Links to **Agent**. The person who created the record. |
| **created** | `Date` | Yes | The date the mapping was asserted. |
| **remarks** | `Text` | Yes | Qualitative notes explaining the mapping logic. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| subjectTaxonConcept | BelongsTo | TaxonConcept | |
| objectTaxonConcept | BelongsTo | TaxonConcept | |
| source | BelongsTo | Reference | |
| creator | BelongsTo | Agent | |

**Enums**

| Field | Vocabulary | Controlled terms |
| --- | --- | --- |
| mapping_relation_id | MappingRelation | `IS_CONGRUENT_WITH`, `INCLUDES`, `IS_INCLUDED_IN`, `PARTIALLY_OVERLAPS`, `IS_DISJOINT_WITH`, `INTERSECTS` |
| taxon_concept_component_id | TaxonConceptComponent | `INTENSIONAL`, `OSTENSIVE` |
| mapping_method_id | MappingMethod | `ASSERTED`, `INFERRED`, `INFERRED_INVERSE`, `INFERRED_CONFIRMED` |

---

## 3. Sidecar Entities

### ExternalIdentityAuthority_EXT

**Table name:** external_identity_authorities_ext \
**Layer:** [4. Authority](layers.md#layer-4-authority-bibliography)

Defines the trusted namespaces for external identifiers (e.g., IPNI, WFO, GBIF).
It maps a system-wide code to a formal reference, ensuring that external IDs are
always contextually grounded.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **reference_id** | `Int` | No | **PK / FK**. Links to the `Reference` Entity. |

---

### Gazetteer_EXT

**Table name:** gazetteers_ext \
**Layer:** [4. Authority](layers.md#layer-4-authority-bibliography)

A spatial sidecar that links references or profiles to a formal geographic
context, such as a state-based gazetteer or a specific mapping grid.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **reference_id** | `Int` | No | **PK / FK**. Links to the `Reference` Entity. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| "reference" | `BelongsTo` | Reference |

---

### Profile

**Table name:** profiles \
**Layer:** [5. Narrative](layers.md#layer-5-narrative-description)

A specialized sidecar that aggregates various data points—narrative sections,
geographic statuses, and images—into a cohesive biological "profile" for a
specific TaxonConcept.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **taxon_concept_id** | `Int` | No | **FK**. The TCS Concept being described. |
status. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **taxonConcept** | `BelongsTo` | TaxonConcept |
| **sections** | `HasMany` | ProfileSection |

---

### Protologue_EXT

**Table name:** protologues_ext \
**Layer:** [4. Authority](layers.md#layer-4-authority-bibliography)

Bibliographic extensions that identify a Reference as a formal taxonomic
authority or a formal state of a taxonomic tree tree.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **reference_id** | `Int` | No | **PK / FK**. Links to the `Reference` Entity. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| "reference" | `BelongsTo` | Reference | |

---

### ScientificName_EXT

**Table name:** scientific_names_ext \
**Layer:** [2a. Core nomenclature](layers.md#layer-2a-core-nomenclature)

A nomenclatural extension that stores metadata exclusive to Linnaean names, such
as formal authorship, publication years, and direct links to protologue
references.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **taxon_name_id** | `Int` | No | FK to `TaxonName`. |
| **authorship** | `String` | Yes | Authorship of the name (e.g., "L."). |
| **published_in_id** | `Int` | Yes | FK to `Reference` (Protologue). |
| **published_in** | `String` | Yes | Protologue string |
| **micro_reference** | 'String` | Yes | Page or figure number within the protologue.
| **year** | `Int` | Yes | Year of publication. |
| **nomenclatural_status_id** | `Int` | Yes | FK to **ControlledTerm** (Status). |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxonName** | `BelongsTo` | TaxonName | |
| **publishedIn** | `BelongsTo` | Reference | |

**Enums**

| Field | Vocabulary | Key terms |
| --- | --- | --- |
| nomenclatural_status_id | NomenclaturalStatus | `LEGITIMATE`, `ILLEGITIMATE`, `SUPERFLUOUS`, `INVALID` |

---

### TaxonTreeNode

**Table name:** taxon_tree_nodes \
**Layer:** [3. Governance](layers.md#layer-3-governance-classification)

A governance map that places a TaxonConcept into a specific position within a
TaxonTree, defining its parent node and rank within that classification.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal PK. |
| **taxon_tree_id** | `Int` | No | The parent tree. |
| **taxon_concept_id** | `Int` | No | The biological meaning assigned to this position. |
| **tree_def_item_id** | `Int` | No | **FK**. Defines which structural level this node sits at. |
| **parent_id** | `Int` | Yes | Adjacency list for hierarchy. |
| **path** | `String` | No | Materialized path for fast recursive queries. |
| **sort_order** | `Int` | Yes | Local sort among siblings. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxonTree** | `BelongsTo` | TaxonTree | | 
| **taxonTreeDefItem** | `BelongsTo` | TaxonTreeDefItem | | 
| **taxonConcept** | `BelongsTo` | TaxonConcept | |

---

### Taxonomy_EXT

**Table name:** taxonomies_ext \
**Layer:** [4. Authority](layers.md#layer-4-authority-bibliography)

Bibliographic extensions that identify a Reference as a formal taxonomic
authority or a point-in-time version of a classification tree.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **reference_id** | `Int` | No | **PK / FK**. Links to the `Reference` Entity. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| "reference" | `BelongsTo` | Reference | |
| "taxonTree" | `HasOne` | TaxonTree | |

---

### TaxonomyVersion_EXT

**Table name:** taxonomy_versions_ext \
**Layer:** [4. Authority](layers.md#layer-4-authority-bibliography)

Bibliographic extensions that identify a Reference as a formal taxonomic
authority or a formal state of a taxonomic tree tree.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **reference_id** | `Int` | No | **PK / FK**. Links to the `Reference` Entity. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| "reference" | `BelongsTo` | Reference | |
| "revisions" | `HasMany` | TaxonTreeRevision | |

---

### ThreatStatusAuthority_EXT

**Table name:** threat_status_authorities_ext \
**Layer:** [4. Authority](layers.md#layer-4-authority-bibliography)

Identifies a Reference as the governing authority for conservation statuses
(e.g., IUCN, EPBC), allowing the system to track threat levels back to their
legal or scientific source.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **reference_id** | `Int` | No | **PK / FK**. Links to the `Reference` Entity. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| "reference" | `BelongsTo` | Reference | |
| "profiles" | `HasManyThrough` | Profile | Profile_Area_MAP |

---

### TraditionalKnowledgeLabel_EXT

**Table name:** traditional_knowledge_labels_ext \
**Layer:** [2a. Core nomenclature](layers.md#layer-2a-core-nomenclature)

A culturally focused extension for managing Indigenous names and labels. it
stores specific rights statements, cultural protocols, and permissions that
govern the use and display of Traditional Knowledge.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **taxon_name_id** | `Int` | No | **PK / FK**. Links to the `TaxonName` Entity. |
| **community_placeholder** | `String` | Yes | **Extension Point**. Name or URI of the community (e.g., "Gunaikurnai"). |
| **protocol_placeholder** | `String` | Yes | **Extension Point**. URI or label of the protocol (e.g., "TK Biocultural Label"). |
| **is_restricted** | `Boolean` | No | Operational flag for API access control. |
| **rights_statement** | `Text` | Yes | Verbatim statement provided by the knowledge holders. |
| **extension_data** | `JSONB` | Yes | **The "Future-Proof" bucket**. Store any extra metadata here. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxonName** | `BelongsTo` | TaxonName | |

---

### Treatment_EXT

**Table name:** treatments_ext \
**Layer:** [4. Authority](layers.md#layer-4-authority-bibliography)

A reference-level extension that is the AccordingTo for an individual Taxon
Concept.

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **reference_id** | `Int` | No | **PK / FK**. Links to the `Reference` Entity. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| "reference" | `BelongsTo` | Reference | |
| "taxonConcept" | `HasOne` | TaxonConcept | |

---

### VernacularName_EXT

**Table name:** vernacular_names_ext \
**Layer:** [2a. Core nomenclature](layers.md#layer-2a-core-nomenclature)

A sidecar for non-scientific naming metadata. It allows for the storage of
language codes, regional usage, and common names without cluttering the
scientific nomenclature tables.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **taxon_name_id** | `Int` | No | **PK / FK**. Links to the `TaxonName` Entity. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxonName** | `BelongsTo` | TaxonName | |

---

## 4. Maps

### Entity_Identity_MAP

**Table name:** entity_identity_map
**Layer:** [6. Identity](layers.md#layer-6-identity)

*Maps the identity to a TaxonName.*

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **entity_type** | `Int` | No | **Type of entity, e.g., TaxonConcept, TaxonName** |
| **taxon_name_id** | `Int` | No | **FK to TaxonName**. |
| **external_identity_id** | `Int` | No | **FK to ExternalIdentity**. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **taxonName** | `BelongsTo` | TaxonName |
| **externalIdentity** | `BelongsTo` | ExternalIdentity |

### NameRelation_MAP

**Table name:** name_relations_map \
**Layer:** [2a. Core nomenclature](layers.md#layer-2a-core-nomenclature)

A syntax-layer map used to track nomenclatural "parentage" and relationships
between names, such as Basionyms, Replaced Names, or Conserved Names.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal primary key. |
| **from_taxon_name_id** | `Int` | No | The subject of the relation (e.g., the New Combination). |
| **to_taxon_name_id** | `Int` | No | The object of the relation (e.g., the Basionym). |
| **name_relation_type_id** | `Int` | No | FK to **ControlledTerm** (e.g., Basionym, Replaced Name). |
| **reference_id** | `Int` | Yes | The reference asserting this nomenclatural relationship (in case of conservation). |
| **remarks** | `Text` | Yes | Notes explaining the relationship. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **fromTaxonName** | `BelongsTo` | TaxonName | |
| **toTaxonName** | `BelongsTo` | TaxonName | |
| **toTaxonName** | `BelongsTo` | TaxonName | |

**Enums**

| Field | Vocabulary | Key terms |
| --- | --- | --- |
| **name_relation_type_id** | `NameRelationType` | `BASIONYM`, `REPLACED_NAME`, `BASED_ON`, `CONSERVED_AGAINST` |

---

### Profile_Area_MAP

**Table name:** profile_area_map \
**Layer:** [8a. Distribution and status](layers.md#layer-8a-distribution-and-status-extension)

Standardized many-to-many join table that associates descriptive profiles with
geographic areas (and their local status).

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **profile_id** | `Int` | No | **FK / PK**. |
| **location_id** | `String` | No | **PK**. Code for the area (e.g., 'A', 'B'). |
| **gazetteer_id** | `Int` | No | **FK to Reference**. Defines the coordinate system/map. |
| **locality** | `String` | Yes | Verbatim name of the area. |
| **occurrence_status_id** | `Int` | Yes | **FK to ControlledTerm**. |
| **establishment_means_id** | `Int` | Yes | **FK to ControlledTerm**. |
| **threat_status_id** | `Int` | Yes | **FK to ControlledTerm**. |
| **is_endemic** | `Boolean` | Yes | Whether or not taxon is endemic to the area. |
| **has_introduced_occurrences** | `Boolean` | Yes | Taxon has introduced ocurrences in the area. |
| **source_id** | `Int` | Yes | **FK to Reference**. Source of the distribution. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **profile** | `BelongsTo` | Profile |
| **gazetteer** | `BelongsTo` | Reference |
| **source** | `BelongsTo` | Reference |


**Enums**

| Field | Vocabulary | Key terms |
| --- | --- | --- |
| **occurrence_status_id** | `OccurrenceStatus` | `PRESENT`, `DOUBTFUL`, `EXTINCT`, `EXCLUDED` |
| **establishment_means_id** | `EstablishmentMeans` | `NATIVE`, `NATIVE_ENDEMIC`, `INTRODUCED` |
| **degree_of_establishment_id** | `DegreeOfEstablishment` | `NATIVE`, `NATURALIZED`, `CULTIVATED` |
| **threat_status_id** | `ThreatStatus` | `EX`, `CR`, `EN`, `VU` |
---

### Profile_Image_MAP

**Table name:** profile_image_map \
**Layer:** [8c. Media](layers.md#layer-8c-media-extension)

Standardized many-to-many join table that associates descriptive profiles with
media assets.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **profile_id** | `Int` | No | **PK / FK**. The parent profile. |
| **image_id** | `Int` | No | **PK / FK**. The image entity. |
| **profile_section_id** | `Int` | **Yes** | **FK to ProfileSection**. If NULL, it's a general profile image. |
| **sort_order** | `Int` | Yes | Order in the profile gallery. |
| **is_hero_image** | `Boolean` | No | Flag for the primary image used in search results/thumbnails. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **profile** | `BelongsTo` | Profile |
| **image** | `BelongsTo` | Specimen |

---

### Profile_Specimen_MAP

**Table name:** profile_specimen_map \
**Layer:** [8b. Voucher](layers.md#layer-8b-voucher-extension)

Standardized many-to-many join table that associates descriptive profiles with
cited voucher specimens.

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **profile_id** | `Int` | No | **PK / FK**. The parent profile citing the specimen. |
| **specimen_id** | `Int` | No | **PK / FK**. The specific specimen entity. |
| **sort_order** | `Int` | Yes | Determines the sequence in "Specimens Examined" lists. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **profile** | `BelongsTo` | Profile |
| **specimen** | `BelongsTo` | Specimen |

---

### TaxonNameUsage_MAP

**Table name:** taxon_name_usages_map \
**Layer:** [2b. Usage](layers.md#layer-2b-usage)

The logic gate between names and concepts. It defines the "role" a name plays
within a concept, such as an Accepted Name, a Synonym, or a Preferred
Vernacular.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **taxon_concept_id** | `Int` | No | FK to **TaxonConcept** (The "Meaning"). |
| **taxon_name_id** | `Int` | No | FK to **TaxonName** (The "Label"). |
| **name_role_id** | `Int` | No | FK to **ControlledTerm** (e.g., Accepted, Synonym). |
| **source_id** | `Int` | Yes | FK to **Reference** asserting this usage. |
| **is_preferred_vernacular_name** | `Boolean` | Yes | Priority flag for common names. |
| **country_code** | `String` | Yes | Regional context for the usage. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxonConcept** | `BelongsTo` | TaxonConcept | |
| **taxonName** | `BelongsTo` | TaxonName | |
| *source* | `BelongsTo` | Reference | |

**Enums**

| Field | Vocabulary | Key Terms |
| --- | --- | --- |
| **name_role_id** | `NameRole` | `ACCEPTED`, `SYNONYM`, `MISAPPLIED`, `VERNACULAR` |

---

### TaxonTree_GeographicScope_MAP

**Table name:** taxon_tree_geographic_scope_map \
**Layer:** [3. Governance](layers.md#layer-3-governance-classification)

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | PK |
| **taxon_tree_id** | `Int` | No | FK to **TaxonTree**. |
| **scope** | `Int` | No | Geographic scope, e.g., Victoria; must match `locality` in **Profile_Area_MAP** record |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxonTree** | `BelongsTo` | TaxonTree | |

---

## 5. Infrastructure

### AuditLog

**Table name:** audit_logs \
**Layer:** [7c. Audit](layers.md#layer-7c-audit)

A granular transaction record. It captures every change made within the system,
storing the "before" and "after" state of a record, the timestamp, and the agent
responsible for the modification.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `bigint` | No | **Primary Key**. |
| **agent_id** | `int` | No | **FK to Agent** who performed the action. |
| **event_type_id** | `int` | No | **FK to ControlledTerm** (e.g., Created, Updated, Deleted). |
| **table_name** | `string` | No | Name of the table affected. |
| **record_id** | `int` | No | The ID of the record that was changed. |
| **old_values** | `jsonb` | Yes | Snapshot of data before the change. |
| **new_values** | `jsonb` | Yes | Snapshot of data after the change. |
| **change_summary** | `text` | Yes | Human-readable explanation of the change. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **agent** | `BelongsTo` | Agent |

---

### ControlledTerm

**Table name:** controlled_terms \
**Layer:** [7b. Vocabulary](layers.md#layer-7b-vocabulary)

The individual atoms of the system's vocabulary. Each term is backed by a
machine-readable IRI, allowing for semantic interoperability with other
international biodiversity standards (SKOS, Dublin Core).

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `int` | No | **Primary Key**. |
| **vocabulary_id** | `int` | No | **FK to ControlledVocabulary**. |
| **label** | `string` | No | Human-readable term (e.g., "Species"). |
| **code** | `string` | No | Short code for logic (e.g., "sp"). |
| **iri** | `string` | No | Term-specific IRI. |
| **sort_order** | `int` | Yes | Ordering for UI display. |
| **is_active** | `boolean` | No | Flag to deprecate terms without deleting data. |
| **metadata** | `jsonb` | Yes | Additional term-specific attributes. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **controlledVocabulary** | `BelongsTo` | ControlledVocabulary |

---

### ControlledVocabulary

**Table name:** controlled_vocabularies \
**Layer:** [7b. Vocabulary](layers.md#layer-7b-vocabulary)

The high-level container for "Picklists." It organizes related terms into
manageable buckets (e.g., "Taxonomic Ranks" or "License Types") to ensure the UI
and API remain consistent.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `int` | No | **Primary Key**. |
| **name** | `string` | No | Human-readable name (e.g., "Taxonomic Rank"). |
| **code** | `string` | No | Machine-readable code. |
| **description** | `text` | Yes | Description. |
| **iri** | `string` | No | Internationalized Resource Identifier for SKOS compliance. |

---

### ProfileDefItem
| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `int` | No | **Primary Key** |
| **taxon_tree_id** | `int` | No | **FK to TaxonTree** |
| **profile_section_type_id** | `int` | No | **FK to ControlledTerm** |
| **is_required** | `bool` | No | Is the section required in a Profile? |
| **sort_order** | `int` | No | Sets the order of sections in a Profile |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **taxonTree** | `BelongsTo` | TaxonTree |

**Enums**

| Field | Vocabulary | Key Terms |
| --- | --- | --- |
| **profile_section_type_id** | `ProfileSectionType` | `DESCRIPTION`, `STATE_DISTRIBUTION`, `HABITAT`, `NOTES` |

---

### Provenance_MIXIN

A foundational utility implemented across all primary resources. It provides a
standardized set of fields—`created_by_id`, `updated_by_id`, `created_at`,
`updated_at`, and `version`—to ensure a complete and defensible audit trail for
the entire data model.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **created_at** | `date` | No | Timestamp of record creation. |
| **updated_at** | `date` | Yes | Timestamp of last modification. |
| **version** | `int` | No | Incremental version number for the record. |
| **created_by_id** | `int` | No | **FK to Agent** who created the record. |
| **updated_by_id** | `int` | Yes | **FK to Agent** who last modified the record. |

**Relationships**

| Relationship | Type | Related model |
| --- | --- | --- |
| **createdBy** | `BelongsTo` | Agent |
| **updatedBy** | `BelongsTo` | Agent |
---

### TaxonTreeDefItem

**Table name:** taxon_tree_def_items \
**Layer:** [3. Governance](layers.md#layer-3-governance-classification)

Defines the valid structural "slots" available within a specific tree. It enforces taxonomic ranks (e.g., Family, Genus, Species) and their allowed hierarchy for a particular classification project.

**Fields**

| Field | Type | Nullable | Description |
| --- | --- | --- | --- |
| **id** | `Int` | No | Internal PK. |
| **taxon_tree_id** | `Int` | No | The tree this rule applies to. |
| **rank_id** | `Int` | Yes | **FK to ControlledTerm**. The formal rank (if applicable). |
| **name** | `String` | No | Display name for the level (e.g., "Group", "Grex"). |
| **rank_order** | `Int` | No | Numeric sort order (e.g., 100 for Family, 500 for Species). |
| **is_required** | `Boolean` | No | Whether this level must be present in a branch. |

**Relationships**

| Relationship | Type | Related model | Map |
| --- | --- | --- | --- |
| **taxon_tree** | `BelongsTo` | TaxonTree | |

**Enums**

| Field | Vocabulary | Key terms |
| --- | --- | --- |
| **rank_id** | `Rank` | `GENUS`, `SPECIES` |

---








