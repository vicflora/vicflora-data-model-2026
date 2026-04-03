<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_codes', function (Blueprint $table) {
            $table->id();
            
            // The physical area this code identifies
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            
            // The authority/reference for this specific code standard
            $table->foreignId('gazetteer_id')->constrained('references'); 
            
            // Hierarchical link within the SAME scheme/standard
            $table->unsignedBigInteger('parent_id')->nullable();
            
            // Scheme metadata
            $table->string('scheme')->index(); // e.g., 'WGSRPD', 'ISO'
            $table->unsignedTinyInteger('level')->nullable(); // e.g., 1, 2, 3, 4
            
            // The identifier
            $table->string('code')->index();
            // $table->boolean('is_primary')->default(false);
            
            // Materialized path for the Code Tree (e.g., '7/78/784')
            $table->string('path')->nullable()->index();

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
            
            // Unique constraint to prevent duplicate entries within a scheme/level
            $table->unique(['scheme', 'level', 'code'], 'unique_scheme_code_idx');
        });


        // Add self-referential constraints after the table exists
        Schema::table('area_codes', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('areas');
        });
    }

    

    public function down(): void
    {
        Schema::dropIfExists('area_codes');
    }
};