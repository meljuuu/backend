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
        Schema::create('subject_grades', function (Blueprint $table) {
            $table->id('Grade_ID');
            $table->unsignedBigInteger('Student_ID');
            $table->unsignedBigInteger('Teacher_ID');
            $table->unsignedBigInteger('Subject_ID');
            $table->floatval('Q1')->nullable();
            $table->floatval('Q2')->nullable();
            $table->floatval('Q3')->nullable();
            $table->floatval('Q4')->nullable();
            $table->floatval('FinalGrade')->nullable();
            $table->enum('Remarks', ['Passed', 'Failed'])->nullable();
            $table->enum('Status', ['Pending', 'Approved', 'Declined'])->default('Pending');
            $table->text('comments')->nullable();
            $table->timestamps();
        
            $table->foreign('Student_ID')->references('Student_ID')->on('students');
            $table->foreign('Teacher_ID')->references('Teacher_ID')->on('teachers');
            $table->foreign('Subject_ID')->references('Subject_ID')->on('subjects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_grades');
    }
};
