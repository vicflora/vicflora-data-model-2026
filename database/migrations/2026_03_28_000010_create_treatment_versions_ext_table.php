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
        Schema::create('treatment_versions_ext', function (Blueprint $table) {
            $table->foreignId('reference_id')->primary()->constrained('references');
            $table->foreignId('treatment_id')->constrained('references');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_versions_ext');
    }
};
