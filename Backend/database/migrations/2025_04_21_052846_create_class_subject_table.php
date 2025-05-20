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
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id('StudentClassSub_ID');
            $table->unsignedBigInteger('Student_ID')->nullable();
            $table->unsignedBigInteger('Class_ID');
            $table->string('SY_ID');
            $table->unsignedBigInteger('Teacher_ID');
            $table->unsignedBigInteger('Subject_ID');
            $table->timestamps();
        
            $table->foreign('Student_ID')->references('Student_ID')->on('students');
            $table->foreign('Class_ID')->references('Class_ID')->on('classes');
            $table->foreign('Teacher_ID')->references('Teacher_ID')->on('teachers');
            $table->foreign('Subject_ID')->references('Subject_ID')->on('subjects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subject');
    }
};
