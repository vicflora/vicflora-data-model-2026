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
        Schema::create('taxonomies_ext', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('references')
                ->onDelete('cascade');
        });

        DB::statement("
            CREATE VIEW taxonomies AS
            SELECT 
                r.*
            FROM public.references r
            JOIN taxonomies_ext ext ON r.id = ext.id
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS taxonomies');
        Schema::dropIfExists('taxonomies_ext');
    }
};
