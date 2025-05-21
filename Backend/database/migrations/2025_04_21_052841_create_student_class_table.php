<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_class', function (Blueprint $table) {
            // Primary key
            $table->id('StudentClass_ID');

            // Foreign keys and related data
            $table->unsignedBigInteger('Student_ID')->nullable();
            $table->unsignedBigInteger('Class_ID')->nullable();
            $table->string('ClassName')->nullable();
            $table->unsignedBigInteger('SY_ID')->nullable();
            $table->unsignedBigInteger('Adviser_ID')->nullable();

            $table->boolean('isAdvisory')->default(false);

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('Student_ID')
                  ->references('Student_ID')->on('students')
                  ->onDelete('cascade');

            $table->foreign('Class_ID')
                  ->references('Class_ID')->on('classes')
                  ->onDelete('cascade');

            $table->foreign('SY_ID')
                  ->references('SY_ID')->on('school_years')
                  ->onDelete('cascade');

            $table->foreign('Adviser_ID')
                  ->references('Teacher_ID')->on('teachers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class');
    }
};