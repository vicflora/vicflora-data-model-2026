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
        Schema::create('profile_area_map', function (Blueprint $table) {
            $table->id();
            
            // The Anchor
            $table->foreignId('profile_id')
                ->constrained('profiles', 'taxon_concept_id');

            // The Sanity Check / Scope
            $table->foreignId('taxon_tree_id')
                ->constrained('taxon_trees');

            // The 'Where' - NOW FORMALIZED
            $table->foreignId('area_code_id')->constrained('area_codes'); 
            $table->foreignId('gazetteer_id')->constrained('references'); 
            $table->string('locality')->nullable(); // For specific place names within the Area

            // The 'Status' (Controlled Terms)
            $table->foreignId('occurrence_status_id')->nullable()->constrained('controlled_terms');
            $table->foreignId('establishment_means_id')->nullable()->constrained('controlled_terms');
            $table->foreignId('degree_of_establishment_id')->nullable()->constrained('controlled_terms');
            $table->foreignId('threat_status_id')->nullable()->constrained('controlled_terms');
            $table->foreignId('threat_status_authority_id')->nullable()->constrained('references');

            // The 'Flags' & Evidence
            $table->boolean('is_endemic')->nullable();
            $table->boolean('has_introduced_occurrences')->nullable();
            $table->string('event_date')->nullable();
            $table->text('occurrence_remarks')->nullable();

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();

            // Refactored composite index
            $table->index(['taxon_tree_id', 'area_code_id', 'gazetteer_id'], 'dist_tree_area_gaz_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_area_map');
    }
};
