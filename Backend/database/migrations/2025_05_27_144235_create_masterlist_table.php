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
        Schema::create('masterlist', function (Blueprint $table) {
            $table->id();
            $table->string('lrn')->unique();
            $table->string('name');
            $table->string('track');
            $table->string('curriculum');
            $table->string('batch');
            $table->enum('status', ['Approved', 'Review', 'Revised', 'Not-Applicable'])->default('Review');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masterlist');
    }
};
