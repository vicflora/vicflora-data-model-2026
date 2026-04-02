<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxon_names', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();

            $table->string('name_string');
            $table->string('language', 10)->nullable();
            $table->unsignedBigInteger('rank_id')->constrained('controlled_terms')->nullable();

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->unsignedBigInteger('created_by_id')->constrained('agents')->nullable();
            $table->unsignedBigInteger('updated_by_id')->constrained('agents')->nullable();
            $table->timestampsTz();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxon_names');
    }
};