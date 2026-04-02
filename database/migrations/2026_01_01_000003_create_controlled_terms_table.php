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
        Schema::create('controlled_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('controlled_vocabulary_id')
                ->constrained()
                ->onDelete('restrict');
            
            $table->string('label');
            $table->string('code');
            $table->unsignedInteger('sort_order')->nullable();
            $table->string('iri')->nullable();
            $table->text('description')->nullable();
            
            $table->timestamps();

            $table->unique(['controlled_vocabulary_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controlled_terms');
    }
};
