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
        Schema::create('taxonomy_versions_ext', function (Blueprint $table) {
            $table->foreignId('reference_id')->primary()->constrained('references');
            $table->foreignId('taxonomy_id')->constrained('references');
        });

        DB::statement("
            CREATE VIEW taxonomy_versions AS
            SELECT 
                r.*,
                tv.taxonomy_id
            FROM public.references r
            JOIN taxonomy_versions_ext tv ON r.id = tv.reference_id
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS taxonomy_versions');
        Schema::dropIfExists('taxonomy_versions_ext');
    }
};
