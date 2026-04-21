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
            $table->string('reference_role')->default('GENERAL');
            $table->foreignId('reference_type_id')->nullable()->constrained('controlled_terms')->onDelete('restrict');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('author_string')->nullable();
            $table->string('year')->nullable();
            $table->text('full_reference_string')->nullable();
            $table->string('short_citation_string')->nullable();
            $table->jsonb('metadata')->nullable();

            // Auditing fields
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
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
