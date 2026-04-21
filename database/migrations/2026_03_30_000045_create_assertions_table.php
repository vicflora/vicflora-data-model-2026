<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assertions', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();
            
            // Reference to the mapping layer
            $table->uuid('occurrence_id')->index();
            
            // The Assertion
            $table->string('term');            // e.g. 'occurrence_status'
            $table->string('asserted_value');  // e.g. 'Native'
            $table->string('reason')->nullable();
            $table->text('remarks')->nullable();
            
            // The Author
            $table->foreignId('agent_id')->constrained('public.agents');
            $table->timestampTz('asserted_at')->useCurrent();

            $table->auditable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assertions');
    }
};