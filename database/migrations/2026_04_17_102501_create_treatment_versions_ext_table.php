<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('treatment_versions_ext', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->nullable();
            $table->foreign('id')
                ->references('id')
                ->on('references')
                ->onDelete('cascade');
            $table->foreignId('treatment_id')->nullable()->constrained('treatments_ext');
            $table->foreignId('taxonomy_id')->constrained('taxonomies');
            $table->foreignId('taxon_concept_id')->constrained('taxon_concepts');
            $table->unsignedSmallInteger('version_number')->nullable();
            $table->string('version_label')->nullable();
            $table->jsonb('data_snapshot')->nullable();

            // Auditing fields
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_versions_ext');
    }
};
