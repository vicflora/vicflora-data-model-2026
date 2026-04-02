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
            $table->timestamps();
            $table->timestamp('modified')->nullable();

            // Core Darwin Core / Identification
            $table->string('basis_of_record')->index();
            $table->string('data_resource_uid')->index();
            $table->string('collection')->nullable();
            $table->string('catalog_number')->nullable();
            $table->string('scientific_name'); // Raw string
            $table->string('recorded_by')->nullable();
            $table->string('record_number')->nullable();
            $table->string('event_date')->nullable();

            // Geography
            $table->string('country')->nullable();
            $table->string('state_province')->nullable();
            $table->text('locality')->nullable();
            $table->text('verbatim_locality')->nullable();
            $table->double('decimal_latitude')->nullable();
            $table->double('decimal_longitude')->nullable();
            
            // Pre-sampled Map Overlays
            $table->string('ibra7_region')->nullable()->index();
            $table->string('ibra7_subregion')->nullable();
            $table->string('lga2023')->nullable()->index();
            $table->string('capad2022')->nullable();
            $table->string('bioregion')->nullable()->index();
            $table->string('park_res')->nullable();
            $table->string('rap')->nullable();

            // Biological Status
            $table->string('establishment_means')->nullable();
            $table->string('degree_of_establishment')->nullable();
            $table->boolean('flowers')->default(false);
            $table->boolean('fruit')->default(false);
            $table->boolean('buds')->default(false);

            // PostGIS Geometry
            $table->geometry('geom', subtype: 'point', srid: 4326)->nullable();

            // Matching Link
            $table->foreignId('parsed_name_id')->nullable()->index();
            $table->string('data_source')->nullable();
        });
        
        // Add a spatial index for mapping performance
        DB::statement('CREATE INDEX occurrences_geom_idx ON mapper.occurrences USING GIST (geom)');
    }

    public function down(): void
    {
        Schema::dropIfExists('mapper.occurrences');
    }
};