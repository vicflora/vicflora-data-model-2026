<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glossary.term_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glossary_id')->constrained('glossary.glossaries');
            $table->foreignId('term_id')->constrained('glossary.terms');
            $table->foreignId('related_term_id')->constrained('glossary.terms');
            
            // Link to Shared ControlledTerm (Synonym, Antonym, Broader, Narrower)
            $table->foreignId('relationship_type_id')->constrained('controlled_terms');
            
            $table->boolean('is_misapplied')->default(false);
            $table->boolean('is_discouraged')->default(false);

            // Versioning, Blameable and Timestamps
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary.term_relationships');
    }
};