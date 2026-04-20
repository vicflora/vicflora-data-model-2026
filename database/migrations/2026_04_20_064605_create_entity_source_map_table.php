<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_source_map', function (Blueprint $table) {
            $table->id();
            
            // sourceable_type and sourceable_id
            // This links to Specimen, TaxonName, etc.
            $table->morphs('sourceable'); 
            
            // The anchor: always points back to the references table
            $table->foreignId('reference_id')
                ->constrained('references')
                ->cascadeOnDelete();
            
            // Metadata for specific citation details (pages, figs, etc.)
            $table->jsonb('metadata')->nullable();
            
            // Blameable for the scientific ledger
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestampsTz();

            // Integrity: An entity can only have a single source
            $table->unique([
                'sourceable_type', 
                'sourceable_id'
            ], 'entity_source_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_source_map');
    }
};