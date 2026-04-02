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
        Schema::create('glossary.terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glossary_id')->constrained('glossary.glossaries')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable(); 

            $table->string('name');
            $table->text('definition');
            $table->string('scope')->nullable();
            $table->boolean('is_discouraged')->default(false);
            $table->string('local_id')->nullable();
            $table->string('language')->default('en');
            $table->string('name_addendum')->nullable();
            
            // Versioning, Blameable and Timestamps
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('shared.agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('shared.agents');
            $table->timestampsTz();

            $table->index(['glossary_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glossary.terms');
    }
};
