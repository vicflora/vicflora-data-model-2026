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
        DB::statement("
            CREATE VIEW references_view AS
            SELECT 
                r.*,
                CASE 
                    WHEN t.id IS NOT NULL THEN 'TAXONOMY'
                    WHEN p.id IS NOT NULL THEN 'PROTOLOGUE'
                    WHEN tr.id IS NOT NULL THEN 'TREATMENT'
                    WHEN trv.id IS NOT NULL THEN 'TREATMENT_VERSION'
                    WHEN tv.id IS NOT NULL THEN 'TAXONOMY_VERSION'
                    WHEN g.id IS NOT NULL THEN 'GAZETTEER'
                    WHEN tsa.id IS NOT NULL THEN 'THREAT_STATUS_AUTHORITY'
                    WHEN eia.id IS NOT NULL THEN 'EXTERNAL_IDENTITY_AUTHORITY'
                    ELSE 'GENERAL'
                END as reference_role
            FROM public.references r
            LEFT JOIN taxonomies_ext t ON r.id = t.id
            LEFT JOIN protologues_ext p ON r.id = p.id
            LEFT JOIN treatments_ext tr ON r.id = tr.id
            Left JOIN treatment_versions_ext trv ON r.id = trv.id
            LEFT JOIN taxonomy_versions_ext tv ON r.id = tv.id
            LEFT JOIN gazetteers_ext g ON r.id = g.id
            LEFT JOIN threat_status_authorities_ext tsa ON r.id = tsa.id
            LEFT JOIN external_identity_authorities_ext eia ON r.id = eia.id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS references_view");
    }
};
