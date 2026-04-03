<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glossary.limitations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Poaceae", "Victoria", "Alpine"
            
            // Versioning, Blameable and Timestamps
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary.limitations');
    }
};