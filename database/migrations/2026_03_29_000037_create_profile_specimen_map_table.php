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
        Schema::create('profile_specimen', function (Blueprint $table) {
            $table->id();

            // The Anchor: Links to the Profile sidecar
            // We use taxon_concept_id as the profile_id
            $table->foreignId('profile_id')
                ->constrained('profiles', 'taxon_concept_id')
                ->onDelete('cascade');

            // The Evidence: Links to the Specimen
            $table->foreignId('specimen_id')
                ->constrained('specimens') 
                ->onDelete('restrict'); // Don't let a specimen be deleted if it's a voucher

            // The Sanity Check: Ensure this link belongs to the correct Tree
            $table->foreignId('taxon_tree_id')
                ->constrained('taxon_trees')
                ->onDelete('no action');

            // Governance: What kind of voucher is this?
            // Links to ControlledTerm (e.g., 'Holotype', 'Voucher', 'Representative')
            $table->foreignId('voucher_type_id')
                ->constrained('controlled_terms')
                ->onDelete('no action');

            // Optional: Link to a specific ProfileSection 
            // (e.g., if a specimen specifically vouchers the 'Distribution' text)
            $table->foreignId('profile_section_id')
                ->nullable()
                ->constrained('profile_sections')
                ->onDelete('set null');

            // Blameable & Versioning
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->timestampsTz();

            // Prevent duplicate vouching of the same specimen for the same profile
            $table->unique(['profile_id', 'specimen_id', 'voucher_type_id'], 'profile_specimen_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_specimen_map');
    }
};
