# Occurrence Module

## The VicFlora Mapper: The Reconciliation Engine

The Mapper module serves as the automated "Customs House" for biological data. It takes raw occurrence records and determines exactly which curated **Taxon Concept** they belong to, even when the names provided are messy, outdated, or informal.

### 1. The "Name to Concept" Pipeline
The power of this module lies in how it handles the transition from a string of text to a biological entity:

* **The ParsedName Layer:** When an `Occurrence` comes in (e.g., from an external data resource), its `scientific_name` is sent to the `ParsedName` table. This breaks the string into its component parts (genus, epithets, authorship).
* **The NameMatch_MAP (The Router):** This is the critical "handshake" table. It maps those parsed strings to your immutable `TaxonName` records. It tracks *how* the match was made (e.g., "exact" (with authorship), "canonical" (without authorship)).
* **The Taxa View (The Controller):** This materialized view acts as the **central router**. It pulls together the accepted usage, the current rank, and the status. It connects the `TaxonName` to the `TaxonConcept` that is currently "Accepted" in VicFlora.

### 2. The Relationship Between Modules

| Module | Responsibility | Core Entity |
| :--- | :--- | :--- |
| **Taxonomy** | Defining the biological "Truth" and how entities relate. | `TaxonConcept`, `TaxonName` |
| **Mapper** | Linking external observations to that "Truth." | `Occurrence`, `ParsedName`, `NameMatch_MAP` |
| **Spatial/Pheno** | Storing derived knowledge about where and when plants grow. | `MapOverlay`, `Phenology_MAP` |

### 3. Derived Insights (The MAP tables)
The Mapper doesn't just route names; it generates the secondary knowledge that makes VicFlora a research-grade tool:

* **Spatial Routing:** Through `TaxonConcept_MapOverlay_MAP`, the system determines which concepts occur in specific Bioregions or LGAs.
* **Phenological Routing:** The `TaxonConcept_Phenology_MAP` (which we debugged earlier!) aggregates thousands of occurrences to tell the user: "This species typically flowers in October."
* **Data Integrity:** The `Assertion` and `Agent` tables allow you to flag occurrences that might be "out of bounds" or misidentified, protecting the integrity of the maps.

---

### Why this is a "Nuke and Pave" Masterpiece
By using the `taxa` materialized view as the router:
1.  **Normalization:** You can store millions of `Occurrences` without bloating your core `TaxonConcepts` table.
2.  **Flexibility:** If the taxonomy changes (e.g., a genus is split), you don't have to edit the occurrences. You simply update the `NameMatch_MAP` or refresh the `taxa` view, and all millions of records "route" to the new correct concept instantly.
3.  **Traceability:** You can see exactly why an occurrence was matched to a name, providing full audit transparency for the census.

```mermaid
---
config:
    layout: elk
---
erDiagram
    Occurrence }o--o| ParsedName : "parsedName"
    ParsedName |o--|{ NameMatch_MAP : parsedName
    NameMatch_MAP }|--o| Taxon : scientificName
    ParsedName }|--o| ControlledTerm : matchType
    Taxon }|--|| TaxonConcept : acceptedNameUsage
    Taxon }|--|| TaxonConcept : species
    Taxon }|--o| TaxonName : scientificName 
    Occurrence ||--|{ TaxonConcept_Occurrence_MAP : occurrence
    TaxonConcept_Occurrence_MAP }|--|| TaxonConcept : taxonConcept
    TaxonConcept }|--o| TaxonName : taxonName

    MapOverlay |o--|{ TaxonConcept_MapOverlay_MAP : area
    TaxonConcept_MapOverlay_MAP }|--o| TaxonConcept : taxonConcept

    TaxonConcept_Phenology_MAP }|--o| TaxonConcept : taxonConcept

    Occurrence |o--|{ Assertion : occurrence
    Assertion }|--o{ Agent : agent

    Occurrence {
        uuid id PK
        string scientific_name
        int parsed_name_id FK "nullable"
        string data_source "nullable"
        string event_date "nullable"

        string establishment_means "nullable"
        string degree_of_establishment "nullable"
        bool flowers "nullable"
        bool fruit "nullable"
        bool buds "nullable"

        point geom "nullable"
        string lga2023 "nullable"
        string bioregion "nullable"
        string park_res "nullable"
        string rap "nullable"

        jsonb metadata "nullable"

        datetime modified
    }

    ParsedName {
        int id PK
        timestamptz(0) created_at "nullable"
        timestamptz(0) updated_at "nullable"
        string scientific_name 
        string type
        jsonb metadata "nullable"
        string canonical_name "nullable"
        string canonical_name_with_marker "nullable"
        string canonical_name_complete "nullable"
        uuid vicflora_scientific_name_id FK "nullable"
    }

    NameMatch_MAP {
      int id PK
      int parsed_name_id FK
      uuid taxon_name_id FK "Points to immutable TaxonName"
      uuid taxon_tree_id FK
      string match_type
    }

    Taxon {
        bigint taxon_id PK "From taxonomic_name_usage_map"
        uuid taxon_concept_id
        uuid scientific_name_id
        string scientific_name
        string scientific_name_authorship "nullable"
        string taxon_rank
        string taxonomic_status
        uuid accepted_name_usage_id "Points to Concept GUID"
        string accepted_name
        uuid species_id "nullable"
        string species_name "nullable"
        string occurrence_status "nullable"
        string establishment_means "nullable"
        string degree_of_establishment "nullable"
    }

    TaxonConcept_Occurrence_MAP {
        uuid taxon_tree_id "uses GUID"
        uuid taxon_concept_id FK "uses GUID"
        uuid occurence_id FK "uses GUID"
    }

    MapOverlay {
        int id PK
        int area_fid 
        string area_code
        string area_name
        multipolygon geom
    }

    TaxonConcept_MapOverlay_MAP {
        uuid taxon_tree_id FK
        uuid taxon_concept_id FK
        int area_id FK
        string occurrence_status 
        string establishment_means
        string degree_of_establishment
    }

    Assertion {
        int id PK
        uuid guid "unique"
        uuid occurrence_id FK
        string reason "nullable"
        text remarks "nullable"
        string term
        string asserted_value
        int agent_id FK
    }

    TaxonConcept_Phenology_MAP {
        int id PK
        uuid taxon_concept_id FK
        int month_numerical
        string month
        int total
        int buds
        int flowers
        int fruit
    }

```