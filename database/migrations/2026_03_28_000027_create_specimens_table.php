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
        Schema::create('specimens', function (Blueprint $table) {
            $table->id();
            $table->string('institution_code', 16)->nullable();
            $table->string('collection_code', 16)->nullable();
            $table->string('catalog_number', 64)->nullable();
            $table->string('source_url')->nullable();
            $table->jsonb('metadata')->nullable();

            $table->auditable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specimens');
    }
};
