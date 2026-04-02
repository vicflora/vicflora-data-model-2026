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
        Schema::create('treatments_ext', function (Blueprint $table) {
            $table->foreignId('reference_id')
                ->primary()
                ->constrained('references')
                ->onDelete('cascade');
            $table->foreignId('taxonomy_version_id')
                ->nullable()
                ->constrained('references')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments_ext');
    }
};
