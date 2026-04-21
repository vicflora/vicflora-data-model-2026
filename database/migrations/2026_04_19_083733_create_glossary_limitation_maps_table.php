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
        Schema::create('glossary.glossary_limitation_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('limitation_id')->constrained('glossary.limitations');
            $table->morphs('limitable');

            $table->auditable();

            $table->unique([
                'limitable_type', 
                'limitable_id', 
                'limitation_id'
            ], 'glossary_limitation_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glossary_limitation_maps');
    }
};
