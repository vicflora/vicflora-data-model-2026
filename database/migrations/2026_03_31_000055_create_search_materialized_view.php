<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = <<<SQL
CREATE MATERIALIZED VIEW search_mv AS
WITH RECURSIVE classification AS (
    -- Root nodes
    SELECT 
        ttn.taxon_concept_id,
        ttn.parent_id,
        ttn.taxon_tree_id,
        tn.name_string,
        r.label AS taxon_rank,
        ttdi.rank_order,
        jsonb_build_object(lower(r.label), tn.name_string) AS lineage,
        ttn.start_date,
        ttn.end_date
    FROM taxon_tree_nodes ttn
    JOIN taxon_concepts tc ON ttn.taxon_concept_id = tc.id
    JOIN taxon_names tn ON tc.taxon_name_id = tn.id
    JOIN taxon_tree_def_items ttdi on ttn.taxon_tree_def_item_id = ttdi.id
    JOIN controlled_terms r ON ttdi.rank_id = r.id
    LEFT JOIN taxon_tree_revisions ttr on ttn.taxon_concept_id = ttr.from_node_id 
    WHERE ttn.parent_id IS NULL

    UNION ALL

    -- Child nodes
    SELECT 
        ttn.taxon_concept_id,
        ttn.parent_id,
        ttn.taxon_tree_id,
        tn.name_string,
        r.label AS taxon_rank,
        ttdi.rank_order,
        cl.lineage || jsonb_build_object(lower(r.label), tn.name_string),
        ttn.start_date,
        ttn.end_date
    FROM taxon_tree_nodes ttn
    JOIN taxon_concepts tc ON ttn.taxon_concept_id = tc.id
    JOIN taxon_names tn ON tc.taxon_name_id = tn.id
    JOIN taxon_tree_def_items ttdi on ttn.taxon_tree_def_item_id = ttdi.id
    JOIN controlled_terms r ON ttdi.rank_id = r.id
    LEFT JOIN taxon_tree_revisions ttr on ttn.taxon_concept_id = ttr.from_node_id 
    JOIN classification cl ON ttn.parent_id = cl.taxon_concept_id
)
SELECT 
    tc.guid AS id,
    tc.guid AS taxon_concept_id,
    tn.guid AS taxon_name_id,
    tn.name_string AS scientific_name,
    
    -- Specific Epithet: 2nd word of the species name in the lineage
    CASE 
        WHEN cl.rank_order >= 220 THEN split_part(cl.lineage->>'species', ' ', 2)
        ELSE NULL 
    END AS specific_epithet,
    
    -- Infraspecific Epithet: Everything after the last space in the full name string
    CASE 
        WHEN cl.rank_order > 220 THEN regexp_replace(tn.name_string, '^.* ', '')
        ELSE NULL 
    END AS infraspecific_epithet,

    CASE 
        WHEN cl.end_date IS NOT NULL AND cl.end_date <= CURRENT_DATE THEN 'historical'
        ELSE 'current'
    END AS status,
    
    cl.lineage->>'kingdom' AS kingdom,
    cl.lineage->>'phylum' AS phylum,
    cl.lineage->>'class' AS class,
    cl.lineage->>'order' AS "order",
    cl.lineage->>'family' AS family,
    cl.lineage->>'genus' AS genus,
    cl.lineage->>'species' AS species,
    
    cl.taxon_rank AS rank,
    
    (
        SELECT jsonb_agg(jsonb_build_object(
            'mapping_relation', mr.code,
            'object_taxon_concept_id', tc_obj.guid,
            'object_taxon_name', tn_obj.name_string,
            'is_direct_replacement', true
        ))
        FROM taxon_concept_mappings tcm
        JOIN taxon_concepts tc_obj ON tcm.object_taxon_concept_id = tc_obj.id
        JOIN taxon_names tn_obj ON tc_obj.taxon_name_id = tn_obj.id
        JOIN controlled_terms mr ON tcm.mapping_relation_id = mr.id
        -- We join classification for the OBJECT to ensure it is current
        JOIN classification cl_obj ON tc_obj.id = cl_obj.taxon_concept_id
        WHERE tcm.subject_taxon_concept_id = tc.id -- Hook to the outer concept
          AND cl.end_date IS NOT NULL             -- Current row (subject) is historical
          AND cl_obj.end_date IS NULL             -- Target row (object) is current
    ) AS mappings,
    
    cl.start_date,
    cl.end_date,
    ref.full_reference_string AS according_to

FROM taxon_concepts tc
JOIN taxon_names tn ON tc.taxon_name_id = tn.id
JOIN "references" ref ON tc.according_to_id = ref.id
JOIN classification cl ON tc.id = cl.taxon_concept_id
WITH DATA
SQL;

        DB::statement($sql);

        DB::statement('CREATE UNIQUE INDEX search_mv_unique_id_idx ON public.search_mv (id)');

        DB::statement('CREATE INDEX search_mv_scientific_name_idx ON public.search_mv (scientific_name)');
        DB::statement('CREATE INDEX search_mv_family_idx ON public.search_mv (family)');
        DB::statement('CREATE INDEX search_mv_status_idx ON public.search_mv (status)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS search_mv');
    }
};
