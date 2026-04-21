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

            $table->auditable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxon_concept_labels_ext');
    }
};