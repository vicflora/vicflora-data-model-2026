<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glossary.term_image_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained('glossary.terms')->onDelete('cascade');
            $table->foreignId('image_id')->constrained('shared.images'); // Adjust schema if needed
            
            $table->string('figure')->nullable(); // e.g., "Fig. 1", "Detail of leaf base"

            // Versioning, Blameable and Timestamps
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('shared.agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('shared.agents');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary.term_images');
    }
};