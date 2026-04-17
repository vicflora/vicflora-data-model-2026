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
            $table->foreignId('reference_id')->primary()->constrained('references');
            $table->foreignId('treatment_id')->constrained('references');
        });
        
        DB::statement("
            CREATE VIEW treatment_versions AS
            SELECT 
                r.*,
                trv.treatment_id
            FROM public.references r
            JOIN treatment_versions_ext trv ON r.id = trv.reference_id
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
