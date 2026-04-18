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
        Schema::create('reference_contributors_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reference_id')->constrained('references')->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            
            // Links to CONTRIBUTOR_ROLE (Author, Editor, Compiler, Illustrator)
            $table->foreignId('contributor_role_id')->constrained('controlled_terms');
            
            // Crucial for Chicago: defines the order (1st author, 2nd author, etc.)
            $table->unsignedSmallInteger('sequence')->default(1);

            $table->unsignedSmallInteger('version')->default(1);
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->timestampsTz();

            // Ensure we don't accidentally add the same agent in the same role twice
            $table->unique(['reference_id', 'agent_id', 'contributor_role_id'], 'ref_agent_role_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_contributors_map');
    }
};
