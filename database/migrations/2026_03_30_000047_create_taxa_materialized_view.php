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
        -- Species Resolution
        CASE WHEN lower(r.label) = 'species' THEN ttn.taxon_concept_id ELSE NULL END as species_id,
        CASE WHEN lower(r.label) = 'species' THEN tn.name_string ELSE NULL END as species_name,
        ttn.start_date,
        ttn.end_date
    FROM taxon_tree_nodes ttn
    JOIN taxon_tree_def_items ttdi ON ttn.taxon_tree_def_item_id = ttdi.id
    JOIN controlled_terms r ON ttdi.rank_id = r.id
    JOIN taxon_concepts tc ON ttn.taxon_concept_id = tc.id
    JOIN taxon_names tn ON tc.taxon_name_id = tn.id
    WHERE ttn.parent_id IS NULL
      AND ttn.end_date IS NULL -- FILTER: Only current roots

    UNION ALL

    -- Child nodes
    SELECT 
        ttn.taxon_concept_id,
        ttn.parent_id,
        ttn.taxon_tree_id,
        tn.name_string,
        r.label AS taxon_rank,
        COALESCE(cl.species_id, CASE WHEN lower(r.label) = 'species' THEN ttn.taxon_concept_id ELSE NULL END) as species_id,
        COALESCE(cl.species_name, CASE WHEN lower(r.label) = 'species' THEN tn.name_string ELSE NULL END) as species_name,
        ttn.start_date,
        ttn.end_date
    FROM taxon_tree_nodes ttn
    JOIN taxon_tree_def_items ttdi ON ttn.taxon_tree_def_item_id = ttdi.id
    JOIN controlled_terms r ON ttdi.rank_id = r.id
    JOIN taxon_concepts tc ON ttn.taxon_concept_id = tc.id
    JOIN taxon_names tn ON tc.taxon_name_id = tn.id
    JOIN classification cl ON ttn.parent_id = cl.taxon_concept_id
    WHERE ttn.end_date IS NULL -- FILTER: Prune historical branches
)
SELECT 
    tnum.id,
    cl.taxon_tree_id,
    tc.id as taxon_concept_id,
    tn.guid as scientific_name_id,
    tn.name_string as scientific_name,
    sne.authorship,
    cl.taxon_rank,
    nur.code as taxonomic_status,
    tc.id as accepted_name_usage_id,
    an.name_string as accepted_name,
    cl.species_id,
    cl.species_name,
    occ.label as occurrence_status,
    est.label as establishment_means,
    deg.label as degree_of_establishment
FROM classification cl 
JOIN taxon_concepts tc ON cl.taxon_concept_id = tc.id
JOIN taxon_name_usages_map tnum ON tc.id = tnum.taxon_concept_id
JOIN controlled_terms nur ON tnum.name_usage_role_id = nur.id
JOIN taxon_names tn ON tnum.taxon_name_id = tn.id
JOIN scientific_names_ext sne ON tn.id = sne.taxon_name_id
JOIN taxon_names an ON tc.taxon_name_id = an.id
JOIN taxon_trees tt ON cl.taxon_tree_id = tt.id
JOIN public.taxon_tree_geographic_scope_map scope ON tt.id = scope.taxon_tree_id
JOIN public.area_codes ac ON scope.scope = ac.code 
    AND scope.gazetteer_id = ac.gazetteer_id
JOIN public.profile_area_map pam ON tc.id = pam.profile_id 
    AND ac.id = pam.area_code_id
LEFT JOIN controlled_terms occ ON pam.occurrence_status_id = occ.id
LEFT JOIN controlled_terms est ON pam.establishment_means_id = est.id
LEFT JOIN controlled_terms deg ON pam.degree_of_establishment_id = deg.id
-- FINAL SAFETY: Ensure only current records make it into the view
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
