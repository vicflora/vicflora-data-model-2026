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
        Schema::create('taxon_tree_geographic_scope_map', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxon_tree_id')->unique();
            $table->string('scope');

            // Audit
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->references('id')->on('agents')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->references('id')->on('agents')->nullOnDelete();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxon_tree_geographic_scope_map');
    }
};
