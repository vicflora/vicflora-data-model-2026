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
        Schema::create('taxon_trees', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();

            $table->string('name');
            $table->boolean('is_published')->default(false);
            $table->foreignId('taxonomy_id')->nullable()->constrained('references');

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->references('id')->on('agents');
            $table->foreignId('updated_by_id')->nullable()->references('id')->on('agents');
            $table->timestampsTz();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxon_trees');
    }
};
