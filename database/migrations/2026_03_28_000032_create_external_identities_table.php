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
        Schema::create('external_identities', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('external_uri')->nullable();
            $table->foreignId('external_identity_authority_id')
                ->constrained('references')
                ->cascadeOnDelete();
            $table->dateTime('last_synced_at')->nullable();
            $table->jsonb('metadata')->nullable();

            // Audit
            $table->timestampsTz();
            $table->foreignId('created_by_id')->nullable()->constrained('agents');
            $table->foreignId('updated_by_id')->nullable()->constrained('agents');
            $table->unsignedSmallInteger('version')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_identities');
    }
};
