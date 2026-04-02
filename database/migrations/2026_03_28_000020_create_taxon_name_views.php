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
                tn.created_at,
                tn.updated_at,
                tn.version,
                tn.guid,
                tn.name_string,
                tn.language,
                tn.rank_id,
                tn.created_by_id,
                tn.updated_by_id,
                sn.authorship,
                sn.published_in_string,
                sn.microreference,
                sn.year,
                sn.published_in_id,
                sn.nomenclatural_code_id,
                sn.nomenclatural_status_id
            FROM taxon_names tn
            JOIN scientific_names_ext sn ON tn.id = sn.taxon_name_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW vernacular_names AS
            SELECT
                tn.id,
                tn.created_at,
                tn.updated_at,
                tn.version,
                tn.guid,
                tn.name_string,
                tn.language,
                tn.rank_id,
                tn.created_by_id,
                tn.updated_by_id
            FROM taxon_names tn
            JOIN vernacular_names_ext vn ON tn.id = vn.taxon_name_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW traditional_knowledge_labels AS
            SELECT
                tn.id,
                tn.created_at,
                tn.updated_at,
                tn.version,
                tn.guid,
                tn.name_string,
                tn.language,
                tn.rank_id,
                tn.created_by_id,
                tn.updated_by_id
            FROM taxon_names tn
            JOIN traditional_knowledge_labels_ext tl ON tn.id = tl.taxon_name_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW taxon_names_view AS
            SELECT 
                tn.id,
                tn.created_at,
                tn.updated_at,
                tn.version,
                tn.guid,
                tn.name_string,
                tn.language,
                tn.rank_id,
                tn.created_by_id,
                tn.updated_by_id,
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
                    WHEN tl.taxon_name_id IS NOT NULL THEN 'TK_LABEL'
                    ELSE 'GENERAL'
                END as name_type
            FROM taxon_names tn
            LEFT JOIN scientific_names_ext sn ON tn.id = sn.taxon_name_id
            LEFT JOIN vernacular_names_ext vn ON tn.id = vn.taxon_name_id
            LEFT JOIN traditional_knowledge_labels_ext tl ON tn.id = tl.taxon_name_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scientific_names');
        Schema::dropIfExists('vernacular_names');
        Schema::dropIfExists('traditional_knowledge_labels');
        Schema::dropIfExists('taxon_names_view');
    }
};
