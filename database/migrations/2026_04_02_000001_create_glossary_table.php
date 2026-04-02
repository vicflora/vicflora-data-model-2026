<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create the physical schema in Postgres
        DB::statement('CREATE SCHEMA IF NOT EXISTS glossary');

        // The high-level Glossary container
        Schema::create('glossary.glossaries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->text('description')->nullable();

            // Versioning, Blameable and Timestamps
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary.glossaries');
        // We typically leave 'CREATE SCHEMA' alone in down() 
        // to prevent accidental cascading deletes of other tables.
    }
};