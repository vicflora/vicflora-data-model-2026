<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE MATERIALIZED VIEW mapper.taxon_concept_phenology_map AS
            WITH monthly_extractions AS (
                SELECT 
                    t.taxon_concept_id,
                    t.scientific_name,
                    -- Extract the month once
                    SUBSTRING(o.event_date FROM 6 FOR 2) AS month_str,
                    o.buds,
                    o.flowers,
                    o.fruit
                FROM mapper.taxa t
                JOIN mapper.taxon_concept_occurrence_map tco ON t.taxon_concept_id = tco.taxon_concept_id
                JOIN mapper.occurrences o ON tco.occurrence_id = o.id
                WHERE o.event_date ~ '^\d{4}-\d{2}'
            )
            SELECT 
                (ROW_NUMBER() OVER (ORDER BY scientific_name, month_str))::integer AS id,
                taxon_concept_id,
                month_str::integer AS month_numerical,
                TO_CHAR(TO_DATE(month_str, 'MM'), 'Month') AS month,
                COUNT(*) AS total,
                COUNT(buds) FILTER (WHERE buds IS NOT NULL) AS buds,
                COUNT(flowers) FILTER (WHERE flowers IS NOT NULL) AS flowers,
                COUNT(fruit) FILTER (WHERE fruit IS NOT NULL) AS fruit
            FROM monthly_extractions
            GROUP BY 
                taxon_concept_id, 
                scientific_name, 
                month_str
            ORDER BY 
                scientific_name, 
                month_numerical
            WITH DATA
        ");

        // Unique index required for CONCURRENT refresh
        DB::statement('CREATE UNIQUE INDEX phenology_taxon_month_unique_idx 
                       ON mapper.taxon_concept_phenology_map (taxon_concept_id, month_numerical)');
        
        // Standard index for the synthetic ID
        DB::statement('CREATE INDEX phenology_id_idx ON mapper.taxon_concept_phenology_map (id)');
    }

    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mapper.taxon_concept_phenology_map');
    }
};