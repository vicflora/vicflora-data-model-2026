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
        Schema::create('mapper.map_overlays', function (Blueprint $table) {
            $table->id();
            $table->string('layer')->index(); // e.g., 'LGA', 'IBRA'
            $table->integer('area_fid')->nullable();
            $table->string('area_code')->nullable();
            $table->string('area_name');
            
            // PostGIS Multipolygon
            $table->geometry('geom', subtype: 'multipolygon', srid: 4326);
            
            $table->auditable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_overlays');
    }
};
