<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scientific_names_ext', function (Blueprint $table) {
            // The Primary Key is the Foreign Key to the base table
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('taxon_names')
                ->onDelete('cascade');
            $table->string('authorship')->nullable();
            $table->string('published_in_string')->nullable();
            $table->string('microreference')->nullable();
            $table->string('year', 10)->nullable();
            $table->foreignId('published_in_id')->nullable()->constrained('references');
            $table->foreignId('nomenclatural_code_id')->nullable()->constrained('controlled_terms');
            $table->foreignId('nomenclatural_status_id')->nullable()->constrained('controlled_terms');

            // Audit fields for the sidecar itself
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scientific_names_ext');
    }
};