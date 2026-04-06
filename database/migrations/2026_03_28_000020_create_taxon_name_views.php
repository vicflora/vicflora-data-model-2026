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
        DB::statement("
            CREATE OR REPLACE VIEW scientific_names AS
            SELECT
                tn.id,
                tn.guid,
                tn.name_string,
                tn.rank_id,
                sn.authorship,
                sn.published_in_string,
                sn.microreference,
                sn.year,
                sn.published_in_id,
                sn.nomenclatural_code_id,
                sn.nomenclatural_status_id,
                tn.version,
                tn.created_by_id,
                tn.updated_by_id,
                tn.created_at,
                tn.updated_at
            FROM taxon_names tn
            JOIN scientific_names_ext sn ON tn.id = sn.taxon_name_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW vernacular_names AS
            SELECT
                tn.id,
                tn.guid,
                tn.name_string,
                tn.rank_id,
                vn.language,
                tn.version,
                tn.created_by_id,
                tn.updated_by_id,
                tn.created_at,
                tn.updated_at
            FROM taxon_names tn
            JOIN vernacular_names_ext vn ON tn.id = vn.taxon_name_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW taxon_names_view AS
            SELECT 
                tn.id,
                tn.guid,
                tn.name_string,
                vn.language,
                tn.rank_id,
                sn.authorship,
                sn.published_in_string,
                sn.microreference,
                sn.year,
                sn.published_in_id,
                sn.nomenclatural_code_id,
                sn.nomenclatural_status_id,
                CASE 
                    WHEN sn.taxon_name_id IS NOT NULL THEN 'SCIENTIFIC'
                    WHEN vn.taxon_name_id IS NOT NULL THEN 'VERNACULAR'
                    ELSE 'GENERAL'
                END as name_type,
                tn.version,
                tn.created_by_id,
                tn.updated_by_id,
                tn.created_at,
                tn.updated_at
            FROM taxon_names tn
            LEFT JOIN scientific_names_ext sn ON tn.id = sn.taxon_name_id
            LEFT JOIN vernacular_names_ext vn ON tn.id = vn.taxon_name_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scientific_names');
        Schema::dropIfExists('vernacular_names');
        Schema::dropIfExists('taxon_names_view');
    }
};
