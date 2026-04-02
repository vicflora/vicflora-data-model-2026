<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxon_name_usages_map', function (Blueprint $table) {
            $table->id();
            
            // The Core Link
            $table->foreignId('taxon_concept_id')->constrained('taxon_concepts');
            $table->foreignId('taxon_name_id')->constrained('taxon_names');
            $table->foreignId('name_usage_role_id')->constrained('controlled_terms');

            // Additional Metadata
            $table->boolean('is_preferred_vernacular_name')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->text('remarks')->nullable();
            
            // Audit (Agents)
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestamps();

            // Indexing for quick lookups of a name's history
            $table->index(['taxon_name_id', 'taxon_concept_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxon_name_usage_map');
    }
};