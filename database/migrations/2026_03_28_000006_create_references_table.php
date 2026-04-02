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
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('title');
            $table->string('full_citation');
            $table->string('author_string')->nullable();
            $table->string('year')->nullable();
            $table->string('doi')->nullable();
            $table->string('url')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->unsignedBigInteger('reference_type_id')->constrained('controlled_terms')->nullable()->onDelete('restrict');
            $table->unsignedBigInteger('created_by_id')->constrained('agents')->nullable()->onDelete('set null');
            $table->unsignedBigInteger('updated_by_id')->constrained('agents')->nullable()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};
