<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scientific_name_usages_map', function (Blueprint $table) {
            $table->id();
            
            // The Core Link
            $table->foreignId('taxon_concept_id')->constrained('taxon_concepts');
            $table->foreignId('taxon_name_id')->constrained('taxon_names');
            $table->foreignId('name_usage_role_id')->constrained('controlled_terms');

            $table->jsonb('metadata')->nullable();
            $table->text('remarks')->nullable();
            
            $table->auditable();

            // Indexing for quick lookups of a name's history
            $table->index(['taxon_name_id', 'taxon_concept_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scientific_name_usages_map');
    }
};