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
        Schema::create('specimen_image_map', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('specimen_id')
                ->constrained('specimens')
                ->onDelete('cascade');

            $table->foreignId('image_id')
                ->constrained('images')
                ->onDelete('cascade');

            // Optional: Herbarium Barcode or Sheet Number
            $table->string('external_id')->nullable()->index();
            $table->integer('sort_order')->default(0);

            $table->timestampsTz();

            // Unique constraint to prevent duplicate mapping
            $table->unique(['specimen_id', 'image_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specimen_image_map');
    }
};
