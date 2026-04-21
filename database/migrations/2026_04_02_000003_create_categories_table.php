<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glossary.categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glossary_id')->constrained('glossary.glossaries');
            
            // Link to the term that defines this category
            $table->foreignId('term_id')->constrained('glossary.terms');
            
            $table->string('name');
            
            $table->auditable();
        });

        // Now we can safely add the foreign key constraint to the terms table
        Schema::table('glossary.terms', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('glossary.categories')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('glossary.terms', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('glossary.categories');
    }
};