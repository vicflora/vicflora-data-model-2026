<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sql = <<<SQL
CREATE MATERIALIZED VIEW mapper.taxon_concept_occurrence_map AS
WITH occurrence_pivot AS (
    -- Link to Accepted Name via the name_match_map bridge
    SELECT 
        t.taxon_concept_id, 
        t.taxon_tree_id,
        o.id AS occurrence_id
    FROM mapper.taxa t
    -- Join on the scientific_name_id (BigInt) to avoid the UUID type mismatch
    JOIN mapper.name_match_map nmm ON t.scientific_name_id = nmm.taxon_name_id
    JOIN mapper.occurrences o ON nmm.parsed_name_id = o.parsed_name_id
    
    UNION
    
    -- Link to Species (Parent) - Rolls up sub-specific occurrences to the species GUID
    SELECT 
        t.species_id AS taxon_concept_id, 
        t.taxon_tree_id,
        o.id AS occurrence_id
    FROM mapper.taxa t
    JOIN mapper.taxa ts ON t.species_id = ts.taxon_concept_id 
        AND t.taxon_tree_id = ts.taxon_tree_id
    -- Again, join using the scientific_name_id (BigInt) from the child taxon
    JOIN mapper.name_match_map nmm ON t.scientific_name_id = nmm.taxon_name_id
    JOIN mapper.occurrences o ON nmm.parsed_name_id = o.parsed_name_id
    WHERE t.species_id IS NOT NULL
)
SELECT 
    (ROW_NUMBER() OVER ())::integer AS id,
    pivot.taxon_concept_id, -- Keep as UUID
    pivot.taxon_tree_id,    -- Keep as UUID
    pivot.occurrence_id,

    -- THE STATUS WATERFALL
    COALESCE(aocc.asserted_value, t.occurrence_status, 'present') AS occurrence_status,
    
    COALESCE(
        CASE WHEN adeg.asserted_value IS NOT NULL THEN 'introduced' ELSE aest.asserted_value END,
        CASE 
            WHEN o.establishment_means IN ('cultivated', 'naturalised') THEN 'introduced'
            ELSE o.establishment_means 
        END, 
        t.establishment_means::text, 
        'native'
    ) AS establishment_means,

    COALESCE(
        adeg.asserted_value,
        CASE 
            WHEN o.establishment_means = 'cultivated' THEN 'cultivated'
            WHEN o.establishment_means = 'naturalised' THEN 'naturalised'
            WHEN o.degree_of_establishment = '' AND o.establishment_means = 'uncertain' THEN 'uncertain'
            WHEN o.degree_of_establishment = '' AND o.establishment_means = 'introduced' THEN 'naturalised'
            WHEN o.degree_of_establishment = 'established' THEN 'naturalised'
            WHEN o.establishment_means = 'native' THEN 'native'
            ELSE o.degree_of_establishment
        END, 
        t.degree_of_establishment, 
        'native'
    ) AS degree_of_establishment

FROM occurrence_pivot pivot
JOIN mapper.occurrences o ON pivot.occurrence_id = o.id
JOIN mapper.taxa t ON pivot.taxon_concept_id = t.taxon_concept_id 
    AND pivot.taxon_tree_id = t.taxon_tree_id
LEFT JOIN public.assertions aocc ON o.id = aocc.occurrence_id AND aocc.term = 'occurrenceStatus'
LEFT JOIN public.assertions aest ON o.id = aest.occurrence_id AND aest.term = 'establishmentMeans'
LEFT JOIN public.assertions adeg ON o.id = adeg.occurrence_id AND adeg.term = 'degreeOfEstablishment'
WITH DATA;
SQL;

        DB::statement($sql);

        // 1. Unique Index for CONCURRENTLY refresh and data integrity
        DB::statement('CREATE UNIQUE INDEX taxon_occurrence_composite_unique_idx 
                       ON mapper.taxon_concept_occurrence_map (taxon_concept_id, occurrence_id)');
        
        // 2. Index for the Synthetic ID (Primary Key)
        DB::statement('CREATE INDEX taxon_occurrence_id_idx ON mapper.taxon_concept_occurrence_map (id)');
        
        // 3. Performance index for taxon-based filtering
        DB::statement('CREATE INDEX taxon_occurrence_taxon_idx ON mapper.taxon_concept_occurrence_map (taxon_concept_id)');
    }

    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mapper.taxon_concept_occurrence_map');
    }
};