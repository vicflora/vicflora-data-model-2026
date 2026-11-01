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
        Schema::table('taxon_tree_revisions', function (Blueprint $table) {
            $table->bigInteger('taxon_concept_mapping_id')
                ->unsigned()
                ->nullable()
                ->unique();

            $table->foreign('taxon_concept_mapping_id')
                ->references('id')
                ->on('taxon_concept_mappings')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxon_tree_revisions', function (Blueprint $table) {
            $table->dropColumn('taxon_concept_mapping_id');
        });
    }
};
