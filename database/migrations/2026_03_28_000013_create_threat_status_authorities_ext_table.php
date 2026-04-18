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
        Schema::create('threat_status_authorities_ext', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('references')
                ->onDelete('cascade');
        });

        DB::statement("
            CREATE VIEW threat_status_authorities AS
            SELECT 
                r.*
            FROM public.references r
            JOIN threat_status_authorities_ext ext ON r.id = ext.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS threat_status_authorities');
        Schema::dropIfExists('threat_status_authorities_ext');
    }
};
