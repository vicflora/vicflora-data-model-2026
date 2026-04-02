<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('taxon_tree_nodes', function (Blueprint $table) {
            // 1. Define the column and set it as the primary key
            $table->foreignId('taxon_concept_id')
                ->primary() // This implicitly creates a unique constraint
                ->constrained('taxon_concepts')
                ->onDelete('restrict');

            $table->foreignId('taxon_tree_id')->constrained('taxon_trees');
            $table->foreignId('taxon_tree_def_item_id')->constrained('taxon_tree_def_items');

            // 2. Define the column without the constraint first
            $table->foreignId('parent_id')->nullable();

            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();

            $table->index(['parent_id', 'taxon_tree_id'], 'ttn_hierarchy_idx');
            $table->index('taxon_concept_id', 'ttn_current_concepts_idx')
                  ->whereNull('end_date');
        });

        // 3. Add the self-referencing constraint AFTER the table structure is defined
        Schema::table('taxon_tree_nodes', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('taxon_concept_id')
                ->on('taxon_tree_nodes');
        });

        DB::statement('
            CREATE INDEX ttn_temporal_range_gist_idx 
            ON taxon_tree_nodes 
            USING GIST (daterange(start_date, end_date, \'[]\'))
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxon_tree_nodes');
    }
};
