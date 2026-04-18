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
        Schema::create('treatment_versions_ext', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->nullable();
            $table->foreign('id')
                ->references('id')
                ->on('references')
                ->onDelete('cascade');
            $table->foreignId('treatment_id')->nullable()->constrained('treatments_ext');
        });
        
        DB::statement("
            CREATE VIEW treatment_versions AS
            SELECT 
                r.*,
                ext.treatment_id
            FROM public.references r
            JOIN treatment_versions_ext ext ON r.id = ext.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS treatment_versions");
        Schema::dropIfExists('treatment_versions_ext');
    }
};
