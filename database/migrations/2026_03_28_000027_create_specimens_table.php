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
            $table->string('recorded_by', 255)->nullable();
            $table->string('record_number', 255)->nullable();
            $table->date('event_date')->nullable();
            $table->string('country', 255)->nullable();
            $table->string('state_province', 255)->nullable();
            $table->string('locality', 255)->nullable();
            $table->decimal('decimal_latitude', 10, 7)->nullable();
            $table->decimal('decimal_longitude', 10, 7)->nullable();
            $table->string('habitat', 255)->nullable();
            $table->string('verbatim_elevation')->nullable();
            $table->string('source_url')->nullable();
            $table->foreignId('external_source_id')
                ->nullable()
                ->constrained('references');

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
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
