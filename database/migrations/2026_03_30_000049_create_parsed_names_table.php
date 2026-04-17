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
        Schema::create('parsed_names', function (Blueprint $table) {
            $table->id();
            
            // The String Identity
            $table->string('scientific_name')->index(); 
            $table->string('canonical_name')->nullable()->index();
            $table->string('canonical_name_with_marker')->nullable();
            $table->string('canonical_name_complete')->nullable();
            
            $table->string('type')->nullable();
            $table->jsonb('metadata')->nullable();

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
        Schema::dropIfExists('parsed_names');
    }
};
