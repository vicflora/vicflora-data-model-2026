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
            $table->morphs('entity');
            
            // The link to the External Authority ID
            $table->foreignId('external_identity_id')
                ->constrained()
                ->cascadeOnDelete();

            // Layer 6 Audit (Blameable)
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();

            // Integrity: An entity shouldn't have the same external ID twice
            $table->unique([
                'entity_type', 
                'entity_id', 
                'external_identity_id'
            ], 'entity_id_map_unique');
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
