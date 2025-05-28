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
        Schema::create('acadbase', function (Blueprint $table) {
            $table->id();
            $table->string('lrn')->unique()->nullable();
            $table->string('name')->nullable();
            $table->enum('track', ['SPJ', 'BEC', 'SPA'])->nullable();
            $table->string('batch')->nullable();
            $table->enum('curriculum', ['JHS', 'SHS'])->nullable();
            $table->enum('status', ['Released', 'Unreleased', 'Not-Applicable', 'Dropped-Out'])
                  ->default('Not-Applicable');
            $table->string('faculty_name')->nullable();
            $table->string('pdf_storage')->nullable();
            $table->date('birthdate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acadbase');
    }
};
