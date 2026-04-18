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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_type_id');
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('initials')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('legal_name')->nullable();
            $table->string('orcid')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users');

            $table->unsignedSmallInteger('version')->default(1);
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->timestampsTz();
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
