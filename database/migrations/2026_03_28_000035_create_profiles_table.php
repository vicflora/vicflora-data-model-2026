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
        Schema::create('profiles', function (Blueprint $table) {
            $table->foreignId('taxon_concept_id')
                ->primary()
                ->constrained('taxon_concepts')
                ->onDelete('restrict');

            
            $table->foreignId('taxon_tree_id')
                ->index() // Essential for tree-scoped lookups
                ->constrained('taxon_trees')
                ->onDelete('no action');

            $table->auditable();

            // Required if you want to use the "Sanity Check" Composite FKs tomorrow
            $table->unique(['taxon_concept_id', 'taxon_tree_id'], 'profile_tree_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
