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
        Schema::create('vernacular_names_ext', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('taxon_names')
                ->onDelete('cascade');
            $table->string('language', 10)->nullable();
        });

        DB::statement("
            CREATE OR REPLACE VIEW vernacular_names AS
            SELECT
                tn.id,
                tn.guid,
                tn.name_string,
                tn.rank_id,
                ext.language,
                tn.version,
                tn.created_by_id,
                tn.updated_by_id,
                tn.created_at,
                tn.updated_at
            FROM taxon_names tn
            JOIN vernacular_names_ext ext ON tn.id = ext.id
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vernacular_names');
        Schema::dropIfExists('vernacular_names_ext');
    }
};
