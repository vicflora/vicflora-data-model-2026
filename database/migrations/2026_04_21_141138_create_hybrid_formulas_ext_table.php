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
        Schema::create('hybrid_formulas_ext', function (Blueprint $table) {
            // The Link to the Hub
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')->references('id')->on('taxon_names')->onDelete('cascade');

            // The Parents (Pointing back to the Hub)
            $table->foreignId('first_hybrid_parent_name_id')->constrained('taxon_names');
            $table->foreignId('second_hybrid_parent_name_id')->constrained('taxon_names');

            // The Standard Audit Block
            $table->auditable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hybrid_formulas_ext');
    }
};
