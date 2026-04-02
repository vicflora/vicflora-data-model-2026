<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE MATERIALIZED VIEW mapper.taxon_concept_map_overlay_map AS
            SELECT 
                (ROW_NUMBER() OVER ())::integer AS id,
                tco.taxon_concept_id,
                tco.taxon_tree_id,
                mo.layer, -- 'bioregions', 'lga', 'park_res', etc.
                mo.id AS map_overlay_id,
                mo.area_name,
                
                -- OCCURRENCE STATUS WATERFALL (Prioritizes 'present'/'endemic')
                (array_agg(tco.occurrence_status ORDER BY 
                    CASE tco.occurrence_status 
                        WHEN 'endemic' THEN 1 WHEN 'present' THEN 2 
                        WHEN 'extinct' THEN 3 WHEN 'doubtful' THEN 4 ELSE 5 
                    END
                ))[1] AS occurrence_status,

                -- ESTABLISHMENT MEANS WATERFALL (Prioritizes 'native')
                (array_agg(tco.establishment_means ORDER BY 
                    CASE tco.establishment_means 
                        WHEN 'native' THEN 1 WHEN 'naturalised' THEN 2 
                        WHEN 'introduced' THEN 3 WHEN 'cultivated' THEN 4 ELSE 5 
                    END
                ))[1] AS establishment_means,

                -- DEGREE OF ESTABLISHMENT WATERFALL (Prioritizes 'invasive'/'established')
                (array_agg(tco.degree_of_establishment ORDER BY 
                    CASE tco.degree_of_establishment 
                        WHEN 'native' THEN 1 WHEN 'invasive' THEN 2 
                        WHEN 'established' THEN 3 WHEN 'reproducing' THEN 4 
                        WHEN 'casual' THEN 5 ELSE 6 
                    END
                ))[1] AS degree_of_establishment

            FROM mapper.taxon_concept_occurrence_map tco
            JOIN mapper.occurrences o ON tco.occurrence_id = o.id
            -- Use the 'layer' logic to join against your MapOverlays table
            JOIN mapper.map_overlays mo ON (
                (mo.layer = 'bioregion' AND o.bioregion = mo.area_name) OR
                (mo.layer = 'lga' AND o.lga2023 = mo.area_name) OR
                (mo.layer = 'park_res' AND o.park_res = mo.area_name)
            )
            GROUP BY 
                tco.taxon_concept_id, 
                tco.taxon_tree_id, 
                mo.layer, 
                mo.id, 
                mo.area_name
            WITH DATA
        ");

        // Unique index for CONCURRENTLY refresh
        DB::statement('CREATE UNIQUE INDEX tc_map_overlay_unique_idx ON mapper.taxon_concept_map_overlay_map (taxon_concept_id, map_overlay_id)');
        
        // Performance indexes
        DB::statement('CREATE INDEX tc_map_overlay_taxon_idx ON mapper.taxon_concept_map_overlay_map (taxon_concept_id)');
        DB::statement('CREATE INDEX tc_map_overlay_layer_idx ON mapper.taxon_concept_map_overlay_map (layer)');
        DB::statement('CREATE INDEX tc_map_overlay_tree_idx ON mapper.taxon_concept_map_overlay_map (taxon_tree_id)');
    }

    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mapper.taxon_concept_map_overlay_map');
    }
};