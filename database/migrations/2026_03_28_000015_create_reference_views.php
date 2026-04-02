<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            CREATE VIEW taxonomies AS
            SELECT 
                r.*
            FROM public.references r
            JOIN taxonomies_ext p ON r.id = p.reference_id
        ");

        DB::statement("
            CREATE VIEW taxonomy_versions AS
            SELECT 
                r.*,
                tv.taxonomy_id
            FROM public.references r
            JOIN taxonomy_versions_ext tv ON r.id = tv.reference_id
        ");

        DB::statement("
            CREATE VIEW treatments AS
            SELECT 
                r.*,
                tr.taxonomy_version_id
            FROM public.references r
            JOIN treatments_ext tr ON r.id = tr.reference_id
        ");

        DB::statement("
            CREATE VIEW treatment_versions AS
            SELECT 
                r.*,
                trv.treatment_id
            FROM public.references r
            JOIN treatment_versions_ext trv ON r.id = trv.reference_id
        ");

        DB::statement("
            CREATE VIEW protologues AS
            SELECT 
                r.*
            FROM public.references r
            JOIN protologues_ext p ON r.id = p.reference_id
        ");

        DB::statement("
            CREATE VIEW gazetteers AS
            SELECT 
                r.*
            FROM public.references r
            JOIN gazetteers_ext p ON r.id = p.reference_id
        ");

        DB::statement("
            CREATE VIEW threat_status_authorities AS
            SELECT 
                r.*
            FROM public.references r
            JOIN threat_status_authorities_ext p ON r.id = p.reference_id
        ");

        DB::statement("
            CREATE VIEW external_identity_authorities AS
            SELECT 
                r.*
            FROM public.references r
            JOIN external_identity_authorities_ext p ON r.id = p.reference_id
        ");

        DB::statement("
            CREATE VIEW references_view AS
            SELECT 
                r.*,
                COALESCE(
                    NULLIF(
                        TRIM(BOTH ', ' FROM 
                            (CASE WHEN t.reference_id IS NOT NULL THEN 'TAXONOMY, ' ELSE '' END ||
                            CASE WHEN p.reference_id IS NOT NULL THEN 'PROTOLOGUE, ' ELSE '' END ||
                            CASE WHEN tr.reference_id IS NOT NULL THEN 'TREATMENT, ' ELSE '' END ||
                            CASE WHEN trv.reference_id IS NOT NULL THEN 'TREATMENT_VERSION, ' ELSE '' END ||
                            CASE WHEN tv.reference_id IS NOT NULL THEN 'TAXONOMY_VERSION, ' ELSE '' END ||
                            CASE WHEN g.reference_id IS NOT NULL THEN 'GAZETTEER, ' ELSE '' END ||
                            CASE WHEN tsa.reference_id IS NOT NULL THEN 'THREAT_STATUS_AUTHORITY, ' ELSE '' END ||
                            CASE WHEN eia.reference_id IS NOT NULL THEN 'EXTERNAL_IDENTITY_AUTHORITY, ' ELSE '' END)
                        ), 
                    ''), 
                'GENERAL') as reference_roles,
                tv.taxonomy_id,
                tr.taxonomy_version_id,
                trv.treatment_id
            FROM public.references r
            LEFT JOIN taxonomies_ext t ON r.id = t.reference_id
            LEFT JOIN protologues_ext p ON r.id = p.reference_id
            LEFT JOIN treatments_ext tr ON r.id = tr.reference_id
            Left JOIN treatment_versions_ext trv ON r.id = trv.reference_id
            LEFT JOIN taxonomy_versions_ext tv ON r.id = tv.reference_id
            LEFT JOIN gazetteers_ext g ON r.id = g.reference_id
            LEFT JOIN threat_status_authorities_ext tsa ON r.id = tsa.reference_id
            LEFT JOIN external_identity_authorities_ext eia ON r.id = eia.reference_id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS references_view");
        DB::statement("DROP VIEW IF EXISTS treatment_versions");
        DB::statement("DROP VIEW IF EXISTS treatments");
        DB::statement("DROP VIEW IF EXISTS taxonomy_versions");
        DB::statement("DROP VIEW IF EXISTS taxonomies");
        DB::statement("DROP VIEW IF EXISTS protologues");
        DB::statement("DROP VIEW IF EXISTS gazetteers");
        DB::statement("DROP VIEW IF EXISTS threat_status_authorities");
        DB::statement("DROP VIEW IF EXISTS external_identity_authorities");
    }
};
