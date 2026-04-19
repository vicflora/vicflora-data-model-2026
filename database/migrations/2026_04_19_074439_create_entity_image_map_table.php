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
        Schema::create('entity_image_map', function (Blueprint $table) {
            $table->id();
            $table->morphs('entity'); // entity_type, entity_id
            $table->foreignId('image_id')->constrained('images')->cascadeOnDelete();
            $table->foreignId('image_role_id')->constrained('controlled_terms');
            
            $table->integer('sort_order')->default(0);

            // Versioning, Blameable and Timestamps
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();

            $table->unique([
                'entity_type', 
                'entity_id', 
                'image_id'
            ], 'entity_image_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_image_map');
    }
};
