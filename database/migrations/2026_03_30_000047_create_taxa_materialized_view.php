<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = <<<SQL
CREATE MATERIALIZED VIEW mapper.taxa AS
WITH RECURSIVE classification AS (
    -- Root nodes
    SELECT 
        ttn.taxon_concept_id,
        ttn.parent_id,
        ttn.taxon_tree_id,
        tn.name_string,
        r.label AS taxon_rank,
        -- Species Resolution (using GUID from the JOINed concept)
        CASE WHEN lower(r.label) = 'species' THEN tc.guid ELSE NULL END as species_guid,
        CASE WHEN lower(r.label) = 'species' THEN tn.name_string ELSE NULL END as species_name,
        ttn.start_date,
        ttn.end_date
    FROM public.taxon_tree_nodes ttn
    JOIN public.taxon_tree_def_items ttdi ON ttn.taxon_tree_def_item_id = ttdi.id
    JOIN public.controlled_terms r ON ttdi.rank_id = r.id
    JOIN public.taxon_concepts tc ON ttn.taxon_concept_id = tc.id
    JOIN public.taxon_names tn ON tc.taxon_name_id = tn.id
    WHERE ttn.parent_id IS NULL
      AND ttn.end_date IS NULL

    UNION ALL

    -- Child nodes
    SELECT 
        ttn.taxon_concept_id,
        ttn.parent_id,
        ttn.taxon_tree_id,
        tn.name_string,
        r.label AS taxon_rank,
        -- Pass down the species GUID or set it if this is the species level
        COALESCE(cl.species_guid, CASE WHEN lower(r.label) = 'species' THEN tc.guid ELSE NULL END) as species_guid,
        COALESCE(cl.species_name, CASE WHEN lower(r.label) = 'species' THEN tn.name_string ELSE NULL END) as species_name,
        ttn.start_date,
        ttn.end_date
    FROM public.taxon_tree_nodes ttn
    JOIN public.taxon_tree_def_items ttdi ON ttn.taxon_tree_def_item_id = ttdi.id
    JOIN public.controlled_terms r ON ttdi.rank_id = r.id
    JOIN public.taxon_concepts tc ON ttn.taxon_concept_id = tc.id
    JOIN public.taxon_names tn ON tc.taxon_name_id = tn.id
    JOIN classification cl ON ttn.parent_id = cl.taxon_concept_id
    WHERE ttn.end_date IS NULL
)
SELECT 
    tnum.id,
    tt.guid as taxon_tree_id,      -- Swapped to GUID
    tc.guid as taxon_concept_id,   -- Swapped to GUID
    tn.guid as scientific_name_id, -- Swapped to GUID
    tn.name_string as scientific_name,
    sne.authorship,
    cl.taxon_rank,
    nur.code as taxonomic_status,
    tc.guid as accepted_name_usage_id, -- Swapped to GUID
    an.name_string as accepted_name,
    cl.species_guid as species_id,     -- Swapped to GUID from CTE
    cl.species_name,
    occ.label as occurrence_status,
    est.label as establishment_means,
    deg.label as degree_of_establishment
FROM classification cl 
JOIN public.taxon_concepts tc ON cl.taxon_concept_id = tc.id
JOIN public.taxon_name_usages_map tnum ON tc.id = tnum.taxon_concept_id
JOIN public.controlled_terms nur ON tnum.name_usage_role_id = nur.id
JOIN public.taxon_names tn ON tnum.taxon_name_id = tn.id
JOIN public.scientific_names_ext sne ON tn.id = sne.taxon_name_id
JOIN public.taxon_names an ON tc.taxon_name_id = an.id
JOIN public.taxon_trees tt ON cl.taxon_tree_id = tt.id
JOIN public.taxon_tree_geographic_scope_map scope ON tt.id = scope.taxon_tree_id
JOIN public.area_codes ac ON scope.scope = ac.code 
    AND scope.gazetteer_id = ac.gazetteer_id
JOIN public.profile_area_map pam ON tc.id = pam.profile_id 
    AND ac.id = pam.area_code_id
LEFT JOIN public.controlled_terms occ ON pam.occurrence_status_id = occ.id
LEFT JOIN public.controlled_terms est ON pam.establishment_means_id = est.id
LEFT JOIN public.controlled_terms deg ON pam.degree_of_establishment_id = deg.id
WHERE cl.end_date IS NULL
WITH DATA;
SQL;
        DB::statement($sql);

        DB::statement("CREATE UNIQUE INDEX taxa_id_idx ON mapper.taxa (id)");
        DB::statement("CREATE INDEX taxa_sci_name_idx ON mapper.taxa (scientific_name)");
        DB::statement("CREATE INDEX taxa_accepted_id_idx ON mapper.taxa (accepted_name_usage_id)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapper.taxa_mv');
    }
};
