<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxon_tree_nodes', function (Blueprint $table) {
            $table->id(); // New surrogate primary key
            
            $table->foreignId('taxon_tree_id')->constrained('taxon_trees');
            $table->foreignId('taxon_concept_id')->constrained('taxon_concepts');
            $table->foreignId('taxon_tree_def_item_id')->constrained('taxon_tree_def_items');
            
            // Reference the node ID, not the concept ID
            $table->foreignId('parent_id')->nullable()->constrained('taxon_tree_nodes');

            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->auditable();

            // Indexes
            $table->index(['parent_id', 'taxon_tree_id'], 'ttn_hierarchy_idx');
            
        });

        DB::statement('
            CREATE UNIQUE INDEX ttn_active_concept_unique_idx 
            ON taxon_tree_nodes (taxon_tree_id, taxon_concept_id) 
            WHERE end_date IS NULL
        ');
        
        DB::statement('
            CREATE INDEX ttn_temporal_range_gist_idx 
            ON taxon_tree_nodes 
            USING GIST (daterange(start_date, end_date, \'[]\'))
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('taxon_tree_nodes');
    }
};
