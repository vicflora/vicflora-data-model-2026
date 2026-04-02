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
        Schema::create('taxon_tree_def_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('taxon_tree_id')->constrained('taxon_trees');
            $table->foreignId('rank_id')->nullable()->constrained('controlled_terms');
            $table->string('name');
            $table->unsignedSmallInteger('rank_order')->nullable();
            $table->boolean('is_required')->default(false);

            // Audit
            $table->timestampsTz();
            $table->foreignId('created_by_id')->nullable()->references('id')->on('agents');
            $table->foreignId('updated_by_id')->nullable()->references('id')->on('agents');
            $table->unsignedSmallInteger('version')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxon_tree_def_items');
    }
};
