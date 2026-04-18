<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scientific_names_ext', function (Blueprint $table) {
            // The Primary Key is the Foreign Key to the base table
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('taxon_names')
                ->onDelete('cascade');
            $table->string('authorship')->nullable();
            $table->string('published_in_string')->nullable();
            $table->string('microreference')->nullable();
            $table->string('year', 10)->nullable();
            $table->foreignId('published_in_id')->nullable()->constrained('references');
            $table->foreignId('nomenclatural_code_id')->nullable()->constrained('controlled_terms');
            $table->foreignId('nomenclatural_status_id')->nullable()->constrained('controlled_terms');

        });

        DB::statement("
            CREATE OR REPLACE VIEW scientific_names AS
            SELECT
                tn.id,
                tn.guid,
                tn.name_string,
                tn.rank_id,
                ext.authorship,
                ext.published_in_string,
                ext.microreference,
                ext.year,
                ext.published_in_id,
                ext.nomenclatural_code_id,
                ext.nomenclatural_status_id,
                tn.version,
                tn.created_by_id,
                tn.updated_by_id,
                tn.created_at,
                tn.updated_at
            FROM taxon_names tn
            JOIN scientific_names_ext ext ON tn.id = ext.id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS scientific_names');
        Schema::dropIfExists('scientific_names_ext');
    }
};