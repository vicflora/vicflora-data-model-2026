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
        Schema::create('scientific_name_author_map', function (Blueprint $table) {
            $table->id();
            // FK to the scientific_names_ext table (Shared PK with references_base)
            $table->foreignId('scientific_name_id')
                ->constrained('scientific_names_ext')
                ->cascadeOnDelete();

            // FK to the specific identity pivot entry that holds the standard_form
            $table->foreignId('agent_id')
                ->constrained('agents');

            // FK to ControlledTerm for COMBINATION, BASIONYM, etc.
            $table->foreignId('author_role_id')->constrained('controlled_terms');

            $table->integer('sequence')->default(1);

            $table->auditable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scientific_name_author_map');
    }
};
