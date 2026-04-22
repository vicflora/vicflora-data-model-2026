<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vernacular_name_usages_map', function (Blueprint $table) {
            $table->id();
            
            // The Core Link
            $table->foreignId('taxon_concept_id')->constrained('taxon_concepts');
            $table->foreignId('taxon_name_id')->constrained('taxon_names');

            // Additional Metadata
            $table->boolean('is_preferred')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->text('remarks')->nullable();
            
            $table->auditable();

            // Integrity Constraint: Only one preferred vernacular name per taxon concept
            $table->unique(['taxon_concept_id', 'is_preferred'], 'unique_preferred_vernacular')
                ->where('is_preferred', true);
            // Indexing for quick lookups of a name's history
            $table->index(['taxon_name_id', 'taxon_concept_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vernacular_name_usage_map');
    }
};