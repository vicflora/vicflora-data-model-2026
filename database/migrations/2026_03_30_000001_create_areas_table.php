<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            
            // Core Identity
            $table->string('name');
            $table->foreignId('area_type_id')->constrained('controlled_terms');
            
            // Hierarchy & Status
            $table->boolean('is_accepted')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('accepted_id')->nullable();

            $table->foreignId('threat_status_authority_id')->nullable()->constrained('threat_status_authorities_ext');
            
            // For fast tree traversal (e.g., '1/5/12')
            $table->string('area_path')->nullable()->index();

            // Versioning, Blameable and Timestamps
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });

        // Add self-referential constraints after the table exists
        Schema::table('areas', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('areas');
            $table->foreign('accepted_id')->references('id')->on('areas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};