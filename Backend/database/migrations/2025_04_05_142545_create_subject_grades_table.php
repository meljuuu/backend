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
            $table->integer('Q1');
            $table->integer('Q2');
            $table->integer('Q3');
            $table->integer('Q4');
            $table->integer('FinalGrade');
            $table->enum('Remarks', ['PASSED', 'FAILED']);

            $table->unsignedBigInteger('Student_ID');
            $table->unsignedBigInteger('Teacher_ID');
            $table->unsignedBigInteger('Coor_ID');
            $table->unsignedBigInteger('Subject_ID');

            $table->foreign('Student_ID')->references('Student_ID')->on('students')->onDelete('cascade');
            $table->foreign('Teacher_ID')->references('Teacher_ID')->on('teachers')->onDelete('cascade');
            $table->foreign('Coor_ID')->references('Coor_ID')->on('coordinators')->onDelete('cascade');
            $table->foreign('Subject_ID')->references('Subject_ID')->on('subjects')->onDelete('cascade');


            $table->timestamps();
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
