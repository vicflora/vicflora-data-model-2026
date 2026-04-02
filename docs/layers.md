# VicFlora Data Model: Layers

## Layer 1: Semantics (Concepts)

The Semantics layer serves as the system's "brain," decoupling biological
identity from nomenclature to permit multiple, potentially conflicting taxonomic
interpretations within the same database. It centers on the **TaxonConcept**,
which represents a specific circumscription explicitly grounded by a scientific
authority (*secundum*), providing a stable semantic anchor for all subsequent
data layers.

### Layer 1a: Concept

The Concept sub-layer defines the fundamental **TaxonConcept** entity, linking
it to a primary name string, an evidentiary reference, and a taxonomic rank to
establish a unique circumscription.

```mermaid
erDiagram
    TaxonConcept }|--|| TaxonName : "taxonName"
    TaxonConcept }|--|| Reference : "accordingTo"
    TaxonConcept }o--|| ControlledTerm : "rank"
    TaxonConcept ||--|{ TaxonConceptMapping : "subjectTaxonConcept"
    TaxonConcept ||--|{ TaxonConceptMapping : "objectTaxonConcept"

    TaxonConcept {
        int id PK
        int taxon_name_id FK 
        int according_to_id FK 
        int rank_id FK "nullable"
        string remarks
    }
```

**Resources:** [TaxonConcept](resources.md#taxonconcept)

### Layer 1b: Mapping

The Mapping sub-layer utilizes the **TaxonConceptMapping** entity to manage
semantic alignments between disparate interpretations, defining relationships
such as congruency, inclusion, or overlap via standardized mapping relations and
methods.

```mermaid
erDiagram
    TaxonConcept ||--|{ TaxonConceptMapping : "subjectTaxonConcept"
    TaxonConcept ||--|{ TaxonConceptMapping : "objectTaxonConcept"
    TaxonConceptMapping }|--|| ControlledTerm : "mappingRelation"
    TaxonConceptMapping }o--|| ControlledTerm : "taxonConceptComponent"
    TaxonConceptMapping }o--|| ControlledTerm : "mappingMethod"
    TaxonConceptMapping }o--|| Reference : "source"
    

    TaxonConceptMapping {
    int id PK
    int subject_taxon_concept_id FK
    int object_taxon_concept_id FK
    int mapping_relation_id FK
    int taxon_concept_component_id FK "nullable"
    int mapping_method_id FK "nullable"
    int source_id FK "nullable"
    int creator_id FK "nullable"
    date created "nullable"
    text remarks "nullable"
}
```

**Resources:** [TaxonConceptMapping](resources.md#taxonconceptmapping)

## Layer 2: Syntax (Names)

The Syntax layer manages the formal linguistic rules and labels used to identify
organisms, treating name strings as discrete records that can be reused across
different concepts. It handles the complexity of nomenclature, synonyms, and
vernaculars, ensuring that every name is correctly categorized by its rank and
nomenclatural status.

### Layer 2a: Core Nomenclature

The Core Nomenclature sub-layer acts as the central registry for name strings,
employing specialized extensions for scientific, vernacular, and Traditional
Knowledge names while tracking syntactic relationships like basionyms through
**NameRelation_MAP**.

```mermaid
---
config:
    layout: elk
---
erDiagram
    TaxonConcept }|--|| TaxonName : "taxonName"
    TaxonConcept }|--|| TaxonName : "acceptedName"
    TaxonConcept }o--|{ TaxonName : "synonyms"
    TaxonConcept }o--|{ TaxonName : "vernacularNames"
    TaxonConcept }o--|| TaxonName : "preferredVernacularName"
    NomenclaturalType }o--|| TaxonName : "typifiedName / typification"
    TaxonName ||--o| ScientificName_EXT : "scientificName"
    ScientificName_EXT }o--|| Reference : "publishedIn"
    TaxonName ||--o| TraditionalKnowledgeLabel_EXT : "traditionalKnowledgeLabel"
    TaxonName ||--o| VernacularName_EXT : "vernacularName"
    TaxonName }o--|| ControlledTerm : "rank"
    ScientificName_EXT }o--|| ControlledTerm: "nomenclaturalStatus"
    
    %% The Syntactic Relationships
    TaxonName ||--o{ NameRelation_MAP : "fromName"
    TaxonName ||--o{ NameRelation_MAP : "toName"
    NameRelation_MAP }|--|| ControlledTerm : "nameRelationType"

    TaxonName {
        int id PK
        int rank_id FK "nullable"
        int nomenclatural_status_id FK "nullable"
        string full_name
        string language "nullable"
    }

    ScientificName_EXT {
        int taxon_name_id FK
        string authorship "nullable"
        int name_published_in_id FK "nullable"
        string name_published_in FK "nullable"
        string micro_reference FK "nullable"
        string year "nullable"
        int nomenclatural_status_id FK
    }

    TraditionalKnowledgeLabel_EXT {
        int taxon_name_id FK
    }

    VernacularName_EXT {
        int taxon_name_id FK
    }

    NameRelation_MAP {
        int from_taxon_name_id FK
        int to_taxon_name_id FK
        int name_relation_type_id FK
        int reference_id FK "nullable"
        string remarks "nullable"
    }
```

**Resources:** [TaxonName](resources.md#taxonname), [ScientificName_EXT](resources.md#scientificname_ext), [TraditionalKnowledgeLabel_EXT](resources.md#traditionalknowledgelabel_ext), [NameRelation_MAP](resources.md#namerelation_map)

### Layer 2b: Usage

The Usage sub-layer uses the **TaxonNameUsage_MAP** to define the functional
role a name plays within a specific concept—such as an "Accepted Name" or
"Synonym"—effectively acting as the logic gate between syntax and semantics.

```mermaid
erDiagram
    TaxonConcept ||--|{ TaxonNameUsage_MAP : "taxonConcept / taxonNameUsages"
    TaxonNameUsage_MAP }|--|| TaxonName : "taxonName / taxonNameUsages"
    TaxonNameUsage_MAP }|--|| ControlledTerm : "nameRole"
    TaxonNameUsage_MAP }o--|| Reference : "source"

    TaxonNameUsage_MAP {
        int taxon_concept_id FK
        int name_role_id FK
        int taxon_name_id FK
        int source_id FK "nullable"
        boolean is_preferred_vernacular_name "nullable"
        string country_code "nullable"
        string usage_notes "nullable"    
    }
```

**Resources:** [TaxonNameUsage_MAP](resources.md#taxonnameusage_map)

### Layer 2c: Typification

The Typification sub-layer formalizes the link between a name and its objective
evidentiary type (either a specimen or another name), recording the formal
publication and source of the typification event.

```mermaid
erDiagram
    TaxonName ||--|{ NomenclaturalType : "typifiedName / typification"
    NomenclaturalType |o--|| TaxonName : "typeName"
    NomenclaturalType |o--|| Specimen : "typeSpecimen"
    NomenclaturalType |o--|| ControlledTerm : "typeOfType"
    NomenclaturalType |o--|| Reference : "publishedIn"
    NomenclaturalType |o--|| Reference : "source"

    NomenclaturalType {
        int id PK
        int typified_name_id FK
        int type_name_id FK "nullable"
        int type_specimen_id FK
        int type_of_type_id FK "nullable"
        int type_published_in FK "nullable"
        int source_id FK "nullable"
        text remarks "nullable"
    }
```

**Resources:** [NomenclaturalType](resources.md#nomenclaturaltype)

## Layer 3: Governance (Classification)

The Governance layer organizes the fluidity of concepts and names into a rigid,
hierarchical structure required for specific administrative or tenant use cases.
By utilizing **TaxonTree** and **TaxonTreeNode** structures, authorities can
curate an "accepted" classification, while **TaxonTreeRevision** ensures a
complete historical log of every taxonomic move, split, or lump.

```mermaid
---
config:
    layout: dagre
---
erDiagram
    TaxonTree ||--o{ TaxonTreeDefItem : "taxonTree"
    TaxonTree ||--o{ TaxonTreeNode : "taxonTree"
    TaxonTree ||--|| TaxonTree_GeographicScope_MAP : taxonTree
    
    TaxonTreeDefItem ||--o{ TaxonTreeNode : "taxonTreeDefItem"
    
    TaxonTreeNode ||--o{ TaxonTreeRevision : "oldNode"
    TaxonTreeNode ||--o{ TaxonTreeRevision : "newNode"
    TaxonTree ||--o{ TaxonTreeRevision : "taxonTree"
    
    TaxonTreeNode }|--|| TaxonConcept : "taxonConcept"
    TaxonTreeNode |o--o| TaxonTreeNode : "parent / children"
    TaxonTreeRevision }o--|| TaxonomyVersion : "taxonomyVersion"
    
    TaxonTree {
        int id PK
        string name
        boolean is_published "nullable"
    }

    TaxonTree_GeographicScope_MAP {
        int id PK
        int taxon_tree_id FK "unique"
        string scope
    }

    TaxonTreeDefItem {
        int id PK
        int taxon_tree_id FK
        int rank_id FK "nullable"
        string name 
        int rank_order "nullable"
        boolean is_required "nullable"
    }

    TaxonTreeNode {
        int id PK
        int taxon_tree_id FK
        int taxon_concept_id FK
        int taxon_tree_def_item_id FK
        int parent_id FK
        string path
        int sort_order "nullable"
    }

    TaxonTreeRevision {
        int id PK
        int taxon_tree_id FK
        int version_id FK "nullable"
        int old_node_id FK "nullable"
        int new_node_id FK "nullable"
        int change_type_id FK
        date effective_date
        text remarks
    }
```

**Resources:** [TaxonTree](resources.md#taxontree), [TaxonTreeDefItem](resources.md#taxontreedefitem), [TaxonTreeNode](resources.md#taxontreenode), [TaxonTreeRevision](resources.md#taxontreerevision), [TaxonTree_GeographicScope_MAP](resources.md#taxontree_geographicscope_map)

## Layer 4: Authority (Bibliography)

The Authority layer provides the evidentiary foundation for the entire system by
managing bibliographic **References**. It uses a sidecar pattern (e.g.,
`Treatment_EXT`, `Protologue_EXT`) to assign specific functional roles to
citations, ensuring every assertion—whether a nomenclatural protologue or a
geographic status—is anchored to a published source.

```mermaid
---
config:
    layout: elk
---
erDiagram
    Protologue_EXT ||--o| Reference : "protologue / reference"
    ExternalIdentityAuthority_EXT ||--o| Reference : "externalReferenceAuthority / reference"
    Taxonomy_EXT ||--o| Reference : "taxonomy / reference"
    TaxonomyVersion_EXT ||--o| Reference : "taxonomyVersion / reference"
    Treatment_EXT ||--o| Reference : "treatment / reference"
    TreatmentVersion_EXT ||--o| Reference : "treatmentVersion / reference"
    Gazetteer_EXT ||--o| Reference : "gazetteer / reference"
    ThreatStatusAuthority_EXT ||--o| Reference : "threatStatusAuthority / reference"

    %%TaxonTree }|--o| Taxonomy_EXT : "taxonomy"
    %%TaxonTreeRevision }|--o| TaxonomyVersion_EXT : "taxonomyVersion"
    %%TaxonConcept }|--o| TaxonomyVersion_EXT : "accordingTo"
    %%TaxonConcept }|--o| Treatment_EXT : "accordingTo"
    %%Profile }|--|| TreatmentVersion_EXT : "treatmentVersion"

    Taxonomy_EXT |o--|{ TaxonomyVersion_EXT : "taxonomy / taxonomyVersions"
    TaxonomyVersion_EXT |o--|{ Treatment_EXT : "taxonomyVersion / treatments"
    Treatment_EXT |o--|{ TreatmentVersion_EXT : "treatment / treatmentVersions"

    Reference }|--|| ControlledTerm : "referenceType"
    Reference ||--o{ TaxonNameUsage_MAP : "source"
    Reference ||--|{ TaxonConcept : "accordingTo"
    Reference ||--o{ TaxonConceptMapping : "source"
    Reference ||--o{ TaxonName : "publishedIn"
    Reference ||--o{ NomenclaturalType : "typePublishedIn"
    Reference ||--o{ NomenclaturalType : "source"
    
    Reference {
        int id PK
        int reference_type_id FK
        string short_title "e.g., Flora of Victoria"
        string full_citation "The complete bibliographic string"
        string author_string "e.g., Walsh, N.G. & Entwisle, T.J."
        string year "e.g., 1999"
        string doi "Digital Object Identifier"
        string uri "Link to PDF or webpage"
        jsonb metadata "Type-specific fields (Volume, Issue, Page)"
    }

    Taxonomy_EXT {
      int reference_id
    }

    TaxonomyVersion_EXT {
      int reference_id PK
      int taxonomy_id FK
    }
    
    Treatment_EXT {
      int reference_id PK
      int taxonomy_version_id FK "nullable"
    }

    TreatmentVersion_EXT {
      int reference_id PK
      int treatment_id FK
    }

    Protologue_EXT {
      int reference_id PK
    }

    Gazetteer_EXT {
      int reference_id PK
    }

    ThreatStatusAuthority_EXT {
      int reference_id PK
    }

    ExternalIdentityAuthority_EXT {
      int reference_id PK
    }

```

**Resources:** [Reference](resources.md#reference), [Taxonomy_EXT](resources.md#taxonomy_ext), [TaxonomyVersion_EXT](resources.md#taxonomyversion_ext), [Treatment_EXT](resources.md#treatment_ext), [Protologue_EXT](resources.md#protologue_ext), [Gazetteer_EXT](resources.md#gazetteer_ext), [ThreatStatusAuthority_EXT](resources.md#threatstatusauthority_ext), [ExternalIdentityAuthority_EXT](resources.md#externalidentityauthority_ext)

## Layer 5: Narrative (Description)

The Narrative layer manages the descriptive content of the system through
modular **ProfileSections** (e.g., Diagnosis, Etymology) aggregated by a
**Profile** sidecar. This layer allows for granular, structured narrative
updates and ensures that descriptive accounts are linked to both a specific
**TaxonConcept** and the evidence-based source of the text.

```mermaid
---
config:
    layout: elk
---
erDiagram
    TaxonTree |o..|{ Profile : "taxonTree"
    TaxonTree |o..|{ ProfileDefItem : "taxonTree"
    TaxonTree |o..|{ ProfileSection : "taxonTree"
    TaxonTree |o..|{ Profile_Specimen_MAP : "taxonTree"
    TaxonTree |o..|{ Profile_Image_MAP : "taxonTree"
    TaxonTree |o..|{ Profile_Area_MAP : "taxonTree"

    TaxonConcept |o--|| Profile : "taxonConcept / profile"
    Profile ||--o{ Profile_Area_MAP : "profile"
    Profile ||--o{ Profile_Image_MAP : "profile"
    Profile ||--o{ Profile_Specimen_MAP : "profile"
    ProfileDefItem ||--o{ ProfileSection : "profile"
    Profile |o--|{ ProfileSection : "profile"
    ProfileSection }|--|| ControlledTerm : "profileSectionType"
    ProfileSection }o--|| Reference : "source"

    Profile {
        int id PK
        int taxon_concept_id FK
    }

    ProfileDefItem {
        int id PK
        int taxontree_id FK
        int profile_section_type_id FK
        bool is_required
        int sort_order

    }

    ProfileSection {
        int id PK
        int profile_id FK
        int profile_section_type_id FK
        int source_id FK "nullable"
        text body_text
        int sort_order "nullable"
    }
```

**Resources:** [Profile](resources.md#profile), [ProfileSection](resources.md#profilesection)

## Layer 6: Identity

The Identity layer facilitates interoperability by managing the mapping between
internal records and external authority systems. Through **ExternalIdentity**
and specialized mapping tables, the system can track persistent identifiers
(GUIDs/URIs) from various global sources, linking them to either names or
concepts as required.

```mermaid
---
config:
  layout: elk
---
erDiagram
    ExternalIdentityAuthority_EXT ||--|{ ExternalIdentity : "externalIdentityAuthority"
    ExternalIdentity ||--|{ Entity_Identity_MAP : "externalIdentity"

    ExternalIdentity {
        int id PK
        int source_system_id FK 
        string external_id
        string external_uri
        timestamp last_synced_at
    }

    Entity_Identity_MAP {
      string entity_type
      int entity_id
      int external_identity_id
    }
```

**Resources:** [ExternalIdentity](resources.md#externalidentity), [Entity_Identity_MAP](resources.md#entity_identity_map)

## Layer 7: Infrastructure

The Infrastructure layer provides the technical "operating system" for the
model, handling the fundamental utilities required for a robust enterprise
application. It combines standard system behaviours with specialized taxonomic
tools to ensure data consistency and long-term defensibility.

### Layer 7a: Agency and Provenance

The Agency and Provenance sub-layer manages human and system **Agents** and
implements the **Any_Entity_MIXIN**, which provides standardized fields for
automated auditing, versioning, and attribution across all primary records.

```mermaid
---
config:
  layout: elk
---
erDiagram
    User ||--o{ Agent : "user"
    Agent }|--|| ControlledTerm : "agentType"
    Agent ||--|{ Any_Entity_MIXIN : "createdBy"
    Agent ||--o{ Any_Entity_MIXIN : "updatedBy"

    Agent {
        int id PK
        int agent_type_id FK
        string name
        string initials
        string orcid "nullable"
        string uri "nullable"
    }

    Any_Entity_MIXIN {
        date created_at 
        date updated_at "nullable"
        int version
        uuid guid "nullable" 
        int created_by_id FK
        int updated_by_id FK "nullable"
    }
```

**Resources:** [Agent](resources.md#agent)

### Layer 7b: Vocabulary

The Vocabulary sub-layer implements a unified **Controlled Vocabulary** pattern,
ensuring that all categorical data is backed by machine-readable IRIs for SKOS
compliance and system-wide terminology consistency.

```mermaid
---
config:
  layout: elk
---
erDiagram
    ControlledVocabulary ||--|{ ControlledTerm : "contains"
    
    ControlledVocabulary {
        int id PK
        string label
        string code
        string iri
    }

    ControlledTerm {
        int id PK
        int vocabulary_id FK
        string label 
        string code
        string iri
        int sort_order "nullable"
        boolean is_active
        jsonb metadata "nullable"
    }
```

**Resources:** [ControlledVocabular](resources.md#controlledvocabulary), [ControlledTerm](resources.md#controlledterm)

### Layer 7c: Audit

This sub-layer utilizes a granular **AuditLog** to capture "before and after"
states of record modifications, providing a high-fidelity transaction record of
all system events.

```mermaid
---
config:
  layout: elk
---
erDiagram
    Agent ||--|{ AuditLog : "performedBy"
    AuditLog }|--|| ControlledTerm : "eventType"

    AuditLog {
        bigint id PK
        int agent_id FK
        int event_type_id FK
        string table_name 
        int record_id 
        jsonb old_values "nullable"
        jsonb new_values "nullable"
        text change_summary "nullable"
    }  
```

**Resources:** [AuditLog](resources.md#auditlog)

## Layer 8: Extension

The Extension layer expands the core model to support specialized biological
sub-domains through one-to-one sidecars and bridge applications. These
extensions allow the system to integrate geographic data, media, and external
identification tools without destabilizing the central taxonomic engine.

### Layer 8a: Distribution and Status (Extension)

The Distribution and Status sub-layer maps concepts to geographic areas via the
**Profile_Area_MAP**, recording localized occurrence status, establishment
means, and threat levels according to standardized gazetteers.

```mermaid
---
config:
  layout: elk
---
erDiagram
    Profile ||--|{ Profile_Area_MAP : "distribution" 
    Profile_Area_MAP }|--|| Reference : "gazetteer"
    Profile_Area_MAP }o--|| Reference : "source"
    Profile_Area_MAP }o--|| ControlledTerm : "occurrenceStatus"
    Profile_Area_MAP }o--|| ControlledTerm : "establishmentMeans"
    Profile_Area_MAP }o--|| ControlledTerm : "degreeOfEstablishment"
    Profile_Area_MAP }o--|| ControlledTerm : "threatStatus"

    Profile_Area_MAP {
        int profile_id FK
        string location_id 
        int gazetteer_id FK 
        string locality "Verbatim name of the area"
        int occurrence_status_id FK "nullable"
        int establishment_means_id FK "nullable"
        int degree_of_establishment_id FK "nullable"
        int threat_status_id FK "nullable"
        boolean is_endemic "nullable"
        boolean has_introduced_occurrences "nullable"
        int source_id FK "nullable - Evidence for this distribution"
        string event_date "nullable"
        string occurrence_remarks "nullable"
    }
```

**Resources:** [Profile_Area_MAP](resources.md#profile_area_map)

### Layer 8b: Voucher (Extension)

The Voucher sub-layer manages the relationship between biological profiles and
cited physical **Specimens**, linking descriptive accounts to verifiable
collection evidence. 

```mermaid
---
config:
  layout: elk
---
erDiagram
    Profile ||--o{ Profile_Specimen_MAP : "profile"
    Profile_Specimen_MAP }o--|| Specimen : "specimen"
    Specimen }o--|| Reference : "externalSource"
    Specimen }o--|{ Specimen_Image_MAP : "specimen"

    Profile_Specimen_MAP {
        int profile_id FK "PK"
        int specimen_id FK "PK"
        int sort_order "nullable"
    }

    Specimen {
        int id PK
        string institution_code "nullable"
        string collection_code "nullable"
        string catalog_number "nullable"
        string recorded_by "nullable"
        string record_number "nullable"
        date event_date "nullable"
        string country "nullable"
        string state_or_province "nullable"
        string locality "nullable"
        double decimal_latitude "nullable"
        double decimal_longitude "nullable"
        string habitat "nullable"
        string verbatim_elevation "nullable"
        string source_url "nullable"
        int external_source_id FK "nullable"
    }
```

**Resources:** [Profile_Specimen_MAP](resources.md#profile_specimen_map), [Specimen](resources.md#specimen)

### Layer 8c: Media (Extension)

The Media sub-layer provides a repository for digital assets, managing
**Images** with full technical metadata, licensing information, and direct
associations to narrative sections.

```mermaid
erDiagram
    Profile ||--o{ Profile_Image_MAP : "profile"
    ProfileSection ||--o{ Profile_Image_MAP : "profileSection"
    Profile_Image_MAP ||--|{ Image : "image"

    Profile_Image_MAP }|--o| ControlledTerm : "imageRole"
    Profile_Image_MAP }|--|| ImageCaption : "caption"
    Image |o--|{ ImageCaption : "image"

    Specimen |o--|{ Specimen_Image_MAP : "specimen"
    Specimen_Image_MAP }|--o| Image : "image"
    
    Image }o--|| ControlledTerm : "imageType"
    Image }o--|| ControlledTerm : "license"

    Image |o--|{ ImageAccessPoint : "image"
    ImageAccessPoint }|--o| ControlledTerm : "variant"

    Profile_Image_MAP {
        int profile_id FK "PK"
        int image_id FK "PK"
        int profile_section_id FK "Optional"
        int taxon_tree_id FK
        int image_role_id FK
        int sort_order
    }

    Specimen_Image_MAP {
      int specimen_id FK
      int image_id FK
      int taxon_tree_id FK
      int image_role_id FK
    }

    Image {
        int id PK
        string uri
        int image_type_id FK
        string scientific_name "nullable"
        string creator
        string rights_holder "nullable"
        string license "nullable"
        jsonb metadata "nullable"
    }

    ImageCaption {
      int id PK
      int image_id FK
      int taxon_tree_id FK
      text caption_body 
      text formatted_caption
    }

    ImageAccessPoint {
      int id PK
      int image_id FK
      int variant_id FK
      string format
      int width
      int height
    }
```

**Resources:** [Profile_Image_MAP](resources.md#profile_image_map), [Image](resources.md#image)

&nbsp;

---

**Layer 8d-g: Sidecar Applications** – These sub-layers act as functional bridges to external models, providing integration points for pathway keys, matrix keys, phylogenetic trees, and occurrence data models (Darwin Core).

---

### Layer 8d: Pathway Key (Sidecar Application)

```mermaid
erDiagram
  TaxonConcept |o--o| Item : "taxonConcept"
  Item ||--o| PathwayKey : "taxonomic scope"
  PathwayKey ||--|{ Item : "keys out" 
```

### Layer 8e: Matrix Key (Sidecar Application)

```mermaid
erDiagram
  TaxonConcept |o--o| Item : "taxonConcept"
  Item ||--o| MatrixKey : "taxonomic scope"
  MatrixKey ||--|{ Item : "keys out" 
```

### Layer 8f: Phylogeny (Sidecar Application)

```mermaid
---
config:
    layout: elk
---
erDiagram
  TaxonConcept ||--o| Clade : "taxonConcept"
  Clade ||--|| PhylogeneticTree : "root"
  Clade }|--|| PhylogeneticTree : "phylogeneticTree"
  Clade ||--o{ Clade : "contains"
  Clade ||--o{ PhylogeneticTreeTip : "contains"
```

### Layer 8g: Occurrences (Sidecar Application)

```mermaid
erDiagram
  TaxonConcept |o--o{ Taxon : TaxonConcept
  Taxon ||--o{ Identification : taxonID
  Identification }o--|| Occurrence : occurrenceID 
```


