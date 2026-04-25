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

            // For fast tree traversal (e.g., '1/5/12')
            $table->string('area_path')->nullable()->index();

            $table->auditable();
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