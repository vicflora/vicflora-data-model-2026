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
        Schema::create('profile_sections', function (Blueprint $table) {
            $table->id();
            
            // Link to the Profile sidecar
            $table->foreignId('profile_id')
                ->constrained('profiles', 'taxon_concept_id');

            // The Sanity Check: Denormalized Tree ID
            $table->foreignId('taxon_tree_id')
                ->constrained('taxon_trees');

            // Link to the Governance Item
            $table->foreignId('profile_def_item_id')
                ->constrained('profile_def_items');

            $table->text('body_text');
            $table->unsignedSmallInteger('sort_order')->nullable();

            $table->auditable();

            // The "Golden" Sanity Check: 
            // A concept should only have one of each Definition Item type
            $table->unique(['profile_id', 'profile_def_item_id'], 'unique_section_per_profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_sections');
    }
};
