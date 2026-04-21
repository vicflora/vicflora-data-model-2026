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
        Schema::create('nomenclatural_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();

            $table->foreignId('typified_name_id')
                ->constrained('taxon_names');
            $table->foreignId('type_of_type_id')
                ->constrained('controlled_terms');
            $table->foreignId('type_name_id')
                ->nullable()
                ->constrained('taxon_names');
            $table->foreignId('type_specimen_id')
                ->nullable()
                ->constrained('specimens');
            $table->foreignId('type_published_in_id')
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
        Schema::dropIfExists('nomenclatural_types');
    }
};
