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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('uri')->unique();
            $table->foreignId('image_type_id')->constrained('controlled_terms');
            $table->string('creator')->nullable();
            $table->text('caption')->nullable();
            $table->string('scientific_name')->nullable();
            $table->string('rights_holder')->nullable();
            $table->string('license')->nullable();
            $table->string('source')->nullable();
            $table->jsonb('metadata')->nullable();
            
            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
