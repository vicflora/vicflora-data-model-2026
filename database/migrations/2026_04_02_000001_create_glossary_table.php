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
        DB::statement('DROP SCHEMA IF EXISTS glossary CASCADE');
        DB::statement('CREATE SCHEMA IF NOT EXISTS glossary');

        // The high-level Glossary container
        Schema::create('glossary.glossaries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->text('description')->nullable();

            $table->auditable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary.glossaries');
        // We typically leave 'CREATE SCHEMA' alone in down() 
        // to prevent accidental cascading deletes of other tables.
    }
};