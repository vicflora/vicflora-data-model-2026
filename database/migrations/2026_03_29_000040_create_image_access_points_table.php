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
        Schema::create('image_access_points', function (Blueprint $table) {
            $table->id();
            
            // Link to the parent Image entity
            $table->foreignId('image_id')
                ->constrained('images')
                ->onDelete('cascade');

            // IMAGE_VARIANT: THUMBNAIL, PREVIEW, HIGHESTRES
            $table->foreignId('variant_id')
                ->constrained('controlled_terms');

            // TDWG AC Fields
            $table->string('access_iri')->comment('The direct URL to the image file');
            $table->string('format')->comment('MIME type, e.g., image/jpeg, image/webp');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            // Optional: File size in bytes for frontend optimization
            $table->bigInteger('file_size')->nullable();

            $table->timestampsTz();

            // Ensure we don't have two thumbnails for the same image
            $table->unique(['image_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_access_points');
    }
};
