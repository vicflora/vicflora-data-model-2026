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
        Schema::create('image_captions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained('images')->onDelete('cascade');
            $table->foreignId('profile_id')->nullable()->constrained('profiles', 'taxon_concept_id');

            // Links the image caption to a taxon tree; the taxon tree acts as a 
            // namespace for the caption, allowing the same image to have 
            // different captions in different trees
            $table->foreignId('taxon_tree_id')->constrained('taxon_trees');

            // Core content
            $table->text('caption_body'); 
            
            // Denormalized field for performance
            $table->text('formatted_caption')->nullable();

            $table->auditable();
            
            $table->unique(['image_id', 'taxon_tree_id', 'profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_captions');
    }
};
