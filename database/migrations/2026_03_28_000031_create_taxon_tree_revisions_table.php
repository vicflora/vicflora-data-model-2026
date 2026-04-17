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
        Schema::create('taxon_tree_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxon_tree_id')->constrained('taxon_trees');
            
            $table->foreignId('from_node_id')->nullable()->constrained('taxon_tree_nodes');
            $table->foreignId('to_node_id')->nullable()->constrained('taxon_tree_nodes');
            
            $table->foreignId('change_type_id')->constrained('controlled_terms');
            
            $table->unsignedBigInteger('taxonomy_version_id')->nullable();
            $table->foreign('taxonomy_version_id')
                ->references('reference_id')
                ->on('taxonomy_versions_ext');

            $table->text('remarks')->nullable();

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxon_tree_revisions');
    }
};
