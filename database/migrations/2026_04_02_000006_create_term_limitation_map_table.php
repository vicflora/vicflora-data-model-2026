<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glossary.term_limitation_map', function (Blueprint $table) {
            $table->foreignId('term_id')->constrained('glossary.terms');
            $table->foreignId('limitation_id')->constrained('glossary.limitations');
            $table->primary(['term_id', 'limitation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary.term_limitation_map');
    }
};