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
        Schema::create('name_relations_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_taxon_name_id')
                ->constrained('taxon_names')
                ->cascadeOnDelete();
            $table->foreignId('to_taxon_name_id')
                ->constrained('taxon_names')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('name_relation_type_id')
                ->constrained('controlled_terms');
            $table->unsignedBigInteger('reference_id')
                ->nullable()
                ->constrained('references');
            $table->text('remarks')->nullable();

            $table->auditable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('name_relations_map');
    }
};
