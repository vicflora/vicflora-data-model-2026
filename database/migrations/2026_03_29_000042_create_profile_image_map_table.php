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
        Schema::create('profile_image_map', function (Blueprint $table) {
            $table->id();
            
            // The Anchor
            $table->foreignId('profile_id')
                ->constrained('profiles', 'taxon_concept_id')
                ->onDelete('cascade');

            // The Sanity Check / Scope
            $table->foreignId('taxon_tree_id')
                ->constrained('taxon_trees');

            // The Media: Links to your asset storage
            $table->foreignId('image_id')
                ->constrained('images')
                ->onDelete('cascade');

            $table->foreignId('image_caption_id')
                ->nullable()
                ->constrained('image_captions')
                ->onDelete('set null');

            $table->foreignId('image_role_id')
                ->constrained('controlled_terms');

            // Display Logic
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->text('caption')->nullable();

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();

            // Ensure an image isn't mapped to the same profile twice
            $table->unique(['profile_id', 'image_id'], 'profile_image_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_image_map');
    }
};
