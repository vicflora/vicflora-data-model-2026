<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure the schema exists
        DB::statement('CREATE SCHEMA IF NOT EXISTS mapper');

        Schema::create('mapper.occurrences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Processed Linkages & Identification
            $table->foreignId('parsed_name_id')->nullable()->index();
            $table->string('scientific_name'); // Raw string for matching
            $table->string('data_source')->index(); // 'VBA' or 'AVH'
            $table->string('event_date')->nullable()->index();

            // Processed Biological Status (Your Filters)
            $table->string('establishment_means')->nullable();
            $table->string('degree_of_establishment')->nullable();
            $table->boolean('buds')->default(false);
            $table->boolean('flowers')->default(false);
            $table->boolean('fruit')->default(false);

            // Processed/Trusted Geography (Your Spatial Filters)
            $table->geometry('geom', subtype: 'point', srid: 4326)->nullable();
            $table->string('lga2023')->nullable()->index();
            $table->string('bioregion')->nullable()->index();
            $table->string('park_res')->nullable()->index();
            $table->string('rap')->nullable()->index();

            // The "Source Truth" Bucket
            $table->jsonb('metadata')->nullable();

            $table->timestamp('modified')->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->timestampsTz();
        });
        
        // Add a spatial index for mapping performance
        DB::statement('CREATE INDEX occurrences_geom_idx ON mapper.occurrences USING GIST (geom)');
    }

    public function down(): void
    {
        Schema::dropIfExists('mapper.occurrences');
    }
};