<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxon_names', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();
            $table->string('name_type')
                ->default('SCIENTIFIC_NAME')
                ->index();
            $table->string('name_string')->index();
            $table->unsignedBigInteger('rank_id')->constrained('controlled_terms')->nullable();

            $table->auditable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxon_names');
    }
};