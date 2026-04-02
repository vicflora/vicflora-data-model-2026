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
        Schema::create('controlled_vocabularies', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('iri')->nullable();
            $table->unsignedBigInteger('created_by_id')->references('id')->on('agents')->onDelete('restrict')->nullable();
            $table->unsignedBigInteger('updated_by_id')->references('id')->on('agents')->onDelete('restrict')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controlled_vocabularies');
    }
};
