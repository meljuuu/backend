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
            $table->string('lrn')->unique();
            $table->string('name');
            $table->enum('track', ['SPJ', 'BEC', 'SPA']);
            $table->string('batch');
            $table->enum('curriculum', ['JHS', 'SHS']);
            $table->enum('status', ['Released', 'Unreleased', 'Not-Applicable', 'Dropped-Out']);
            $table->string('faculty_name');
            $table->string('pdf_storage')->nullable();
            $table->date('birthdate');
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
