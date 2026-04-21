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
        Schema::create('profile_def_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('taxon_tree_id')
                ->constrained('taxon_trees')
                ->onDelete('no action');

            $table->foreignId('profile_section_type_id')
                ->constrained('controlled_terms')
                ->onDelete('no action');

            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->auditable();
            
            // Ensure a tree doesn't define the same section type twice
            $table->unique(['taxon_tree_id', 'profile_section_type_id'], 'tree_section_unique');        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_def_items');
    }
};
