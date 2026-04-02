<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE MATERIALIZED VIEW mapper.name_match_map AS
            SELECT 
                (ROW_NUMBER() OVER ())::integer AS id,
                t.scientific_name_id AS taxon_name_id, -- Points to public.taxon_names.guid
                t.id AS taxon_concept_id,             -- Kept for quick filtering
                pn.id AS parsed_name_id,               -- Points to mapper.parsed_names.id
                CASE 
                    WHEN t.scientific_name = pn.scientific_name THEN 'EXACT'
                    WHEN t.scientific_name = pn.canonical_name_complete THEN 'EXACT'
                    WHEN t.scientific_name = pn.canonical_name_with_marker THEN 'CANONICAL'
                    ELSE NULL
                END AS match_type
            FROM mapper.taxa t
            JOIN public.parsed_names pn ON (
                t.scientific_name = pn.scientific_name OR
                t.scientific_name = pn.canonical_name_complete OR 
                t.scientific_name = pn.canonical_name_with_marker
            )
            WITH DATA
        ");

        // Unique index on the Name + Parsed pair
        DB::statement('CREATE UNIQUE INDEX name_match_unique_idx ON mapper.name_match_map (taxon_name_id, parsed_name_id)');
        
        // Performance indexes
        DB::statement('CREATE INDEX name_match_taxon_name_idx ON mapper.name_match_map (taxon_name_id)');
        DB::statement('CREATE INDEX name_match_concept_idx ON mapper.name_match_map (taxon_concept_id)');
        DB::statement('CREATE INDEX name_match_parsed_idx ON mapper.name_match_map (parsed_name_id)');
    }

    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mapper.name_match_map');
    }
};