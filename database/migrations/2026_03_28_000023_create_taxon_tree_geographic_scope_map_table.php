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
            
            // The link to the Taxon Tree
            $table->foreignId('taxon_tree_id')->constrained('taxon_trees');

            // The Authority/Standard for the scope (e.g., WGSRPD or ISO)
            $table->foreignId('gazetteer_id')->constrained('references'); 

            // The specific code (e.g., 'VIC', '78')
            $table->string('scope');

            $table->auditable();

            // Composite index for high-performance lookups and to prevent duplicates
            $table->unique(['taxon_tree_id', 'gazetteer_id', 'scope'], 'tree_scope_authority_unique');
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
