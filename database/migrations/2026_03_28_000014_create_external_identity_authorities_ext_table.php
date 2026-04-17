<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_identity_authorities_ext', function (Blueprint $table) {
            $table->foreignId('reference_id')->primary()->constrained('references');
        });

        DB::statement("
            CREATE VIEW external_identity_authorities AS
            SELECT 
                r.*
            FROM public.references r
            JOIN external_identity_authorities_ext p ON r.id = p.reference_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS external_identity_authorities');
        Schema::dropIfExists('external_identity_authorities_ext');
    }
};
