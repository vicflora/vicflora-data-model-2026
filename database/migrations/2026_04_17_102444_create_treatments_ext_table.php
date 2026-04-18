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
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('references')
                ->onDelete('cascade');
            $table->foreignId('taxonomy_id')->nullable()->constrained('taxonomies_ext');
            $table->foreignId('taxon_concept_id')->unique()->constrained('taxon_concepts');
        });

        DB::statement("
            CREATE VIEW treatments AS
            SELECT 
                r.*,
                ext.taxonomy_id,
                ext.taxon_concept_id
            FROM public.references r
            JOIN treatments_ext ext ON r.id = ext.id
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
