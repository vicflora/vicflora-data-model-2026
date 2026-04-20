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
        Schema::create('taxon_concept_mappings', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();
            
            // Core mapping
            $table->foreignId('subject_taxon_concept_id')->constrained('taxon_concepts');
            $table->foreignId('object_taxon_concept_id')->constrained('taxon_concepts');
            $table->foreignId('mapping_relation_id')->constrained('controlled_terms');

            // Additional Metadata
            $table->foreignId('taxon_concept_component_id')->nullable()->constrained('controlled_terms');
            $table->foreignId('mapping_method_id')->nullable()->constrained('controlled_terms');
            $table->text('remarks')->nullable();

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->references('id')->on('agents')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->references('id')->on('agents')->nullOnDelete();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxon_concept_mappings');
    }
};
