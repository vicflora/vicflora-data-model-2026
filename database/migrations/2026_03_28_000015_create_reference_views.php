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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS taxonomy_versions");
        DB::statement("DROP VIEW IF EXISTS taxonomies");
        DB::statement("DROP VIEW IF EXISTS protologues");
        DB::statement("DROP VIEW IF EXISTS gazetteers");
        DB::statement("DROP VIEW IF EXISTS threat_status_authorities");
        DB::statement("DROP VIEW IF EXISTS external_identity_authorities");
    }
};
