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
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('references')
                ->onDelete('cascade');
            $table->foreignId('taxonomy_id')->constrained('taxonomies_ext');
        });

        DB::statement("
            CREATE VIEW taxonomy_versions AS
            SELECT 
                r.*,
                ext.taxonomy_id
            FROM public.references r
            JOIN taxonomy_versions_ext ext ON r.id = ext.id
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
