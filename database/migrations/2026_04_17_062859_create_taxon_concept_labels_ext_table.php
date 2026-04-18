<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxon_concept_labels_ext', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('taxon_names')
                ->onDelete('cascade');

            // The 'Base Name' (The pure nomenclatural TaxonName)
            $table->foreignId('base_name_id')
                ->constrained('taxon_names')
                ->onDelete('restrict');

            // The Concept providing the accordingTo authority
            $table->foreignId('taxon_concept_id')
                ->constrained('taxon_concepts')
                ->onDelete('cascade');

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });

        DB::statement("
            CREATE OR REPLACE VIEW taxon_concept_labels AS
            SELECT
                tn.id,
                tn.guid,
                tn.name_string,
                tn.rank_id,
                ext.base_name_id,
                ext.taxon_concept_id,
                tn.version,
                tn.created_by_id,
                tn.updated_by_id,
                tn.created_at,
                tn.updated_at
            FROM taxon_names tn
            JOIN taxon_concept_labels_ext ext ON tn.id = ext.id
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('taxon_concept_labels_ext');
    }
};