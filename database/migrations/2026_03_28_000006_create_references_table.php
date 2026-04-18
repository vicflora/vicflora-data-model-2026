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
            $table->uuid('guid');
            $table->foreignId('reference_type_id')->nullable()->constrained('controlled_terms')->onDelete('restrict');
            $table->string('title');
            $table->string('author_string')->nullable();
            $table->string('year')->nullable();
            $table->text('full_reference_string')->nullable();
            $table->string('short_citation_string')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->timestampsTz();
            $table->unsignedSmallInteger('version')->default(1);
            $table->unsignedBigInteger('created_by_id')->constrained('agents')->nullable()->onDelete('set null');
            $table->unsignedBigInteger('updated_by_id')->constrained('agents')->nullable()->onDelete('set null');
        });

        Schema::table('references', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('references');
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
