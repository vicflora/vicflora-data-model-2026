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
        Schema::create('threat_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scientific_name_id')->constrained('taxon_names');
            $table->foreignId('threat_status_authority_id')->constrained('threat_status_authorities_ext');
            $table->foreignId('area_id')->constrained();
            
            $table->foreignId('status_term_id')->constrained('controlled_terms'); 
            
            $table->jsonb('metadata')->nullable(); 
            $table->text('remarks')->nullable();
            $table->auditable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threat_statuses');
    }
};
