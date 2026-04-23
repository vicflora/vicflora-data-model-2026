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
            $table->morphs('depictable');
            $table->foreignId('image_id')->constrained('images')->cascadeOnDelete();
            $table->foreignId('image_role_id')->constrained('controlled_terms');
            
            $table->integer('sort_order')->default(0);

            $table->auditable();

            $table->unique([
                'depictable_type', 
                'depictable_id', 
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
