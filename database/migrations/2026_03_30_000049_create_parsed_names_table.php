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
        Schema::create('parsed_names', function (Blueprint $table) {
            $table->id();
            
            // The String Identity
            $table->string('scientific_name')->index(); 
            $table->string('canonical_name')->nullable()->index();
            $table->string('canonical_name_with_marker')->nullable();
            $table->string('canonical_name_complete')->nullable();
            
            // Atomized Components
            $table->string('type')->nullable(); // e.g., SCIENTIFIC, HYBRID
            $table->string('genus_or_above')->nullable();
            $table->string('specific_epithet')->nullable();
            $table->string('infraspecific_epithet')->nullable();
            $table->string('rank_marker')->nullable(); // var., subsp.
            $table->string('authorship')->nullable();
            
            // Parsing Metadata
            $table->boolean('parsed')->default(false);
            $table->boolean('parsed_partially')->default(false);
            $table->string('key')->nullable(); // MD5 or unique hash of the name
            
            // Legacy/VicFlora Resolution (Nullable, as we use NameMatch_MAP now)
            $table->uuid('vicflora_scientific_name_id')->nullable();
            $table->string('name_match_type')->nullable();
            
            $table->text('remarks')->nullable();

            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parsed_names');
    }
};
