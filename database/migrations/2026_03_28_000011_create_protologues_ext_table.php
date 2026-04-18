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
        Schema::create('protologues_ext', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')
                ->references('id')
                ->on('references')
                ->onDelete('cascade');
            $table->string('in_authors')->nullable();
            $table->text('protologue_string')->nullable();
        });

        DB::statement("
            CREATE VIEW protologues AS
            SELECT 
                r.*,
                ext.in_authors,
                ext.protologue_string
            FROM public.references r
            JOIN protologues_ext ext ON r.id = ext.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS protologues');
        Schema::dropIfExists('protologues_ext');
    }
};
