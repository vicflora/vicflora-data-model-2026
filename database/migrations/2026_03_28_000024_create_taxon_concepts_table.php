<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxon_concepts', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();
            
            $table->foreignId('taxon_tree_id')->nullable()->constrained('taxon_trees');
            $table->foreignId('taxon_name_id')->constrained('taxon_names');
            $table->foreignId('according_to_id')->constrained('references');
            
            $table->foreignId('rank_id')->nullable()->constrained('controlled_terms');
           
            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();

            // Compound index for faster tree-specific concept lookups
            $table->index(['taxon_tree_id', 'id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxon_concepts');
    }
};