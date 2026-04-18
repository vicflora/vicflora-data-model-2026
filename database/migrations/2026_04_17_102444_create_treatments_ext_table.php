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
        Schema::create('treatments_ext', function (Blueprint $table) {
            $table->foreignId('reference_id')->primary()->constrained('references');
            $table->foreignId('taxonomy_id')->nullable()->constrained('references');
            $table->foreignId('taxon_concept_id')->unique()->constrained('taxon_concepts');
        });

        DB::statement("
            CREATE VIEW treatments AS
            SELECT 
                r.*,
                tr.taxonomy_version_id,
                tr.taxon_concept_id
            FROM public.references r
            JOIN treatments_ext tr ON r.id = tr.reference_id
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS treatments");
        Schema::dropIfExists('treatments_ext');
    }
};
