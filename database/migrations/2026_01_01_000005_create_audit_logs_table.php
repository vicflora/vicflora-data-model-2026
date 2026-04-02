<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // The Event Type (from our Enum)
            $table->string('event_type');

            // Polymorphic relation to the record being audited
            // Creates auditable_type (string) and auditable_id (bigint)
            $table->morphs('auditable');

            // The Agent/User responsible for the change
            $table->foreignId('agent_id')
                  ->constrained('agents')
                  ->onDelete('restrict');

            // Data Snapshots
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Contextual Metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // We only need the creation timestamp
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};