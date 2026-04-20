<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_creator_map', function (Blueprint $table) {
            $table->id();
            
            // createable_type and createable_id
            $table->morphs('createable'); 
            
            // The Agent who historically created the entity
            $table->foreignId('agent_id')
                ->constrained('agents')
                ->cascadeOnDelete();
            
            // dcterms:created - historical date of creation
            $table->string('created_at_date')->nullable();
            
            $table->jsonb('metadata')->nullable();
            
            // Blameable (who entered this creator link into the system)
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestampsTz();

            // Integrity Lock: One entity, one historical creator.
            $table->unique([
                'createable_type', 
                'createable_id'
            ], 'createable_entity_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_creator_map');
    }
};