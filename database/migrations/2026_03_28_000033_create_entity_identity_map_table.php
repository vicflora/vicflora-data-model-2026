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
        Schema::create('entity_identity_map', function (Blueprint $table) {
            $table->id();
            
            // The link to the Name or Concept
            $table->morphs('linkable');
            
            // The link to the External Authority ID
            $table->foreignId('external_identity_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->auditable();

            // Integrity: An entity shouldn't have the same external ID twice
            $table->unique([
                'linkable_type', 
                'linkable_id', 
                'external_identity_id'
            ], 'linkable_id_map_unique');
        });   
 }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_identity_map');
    }
};
