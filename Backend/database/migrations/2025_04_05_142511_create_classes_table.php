<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id('Class_ID');
            $table->string('ClassName');
            $table->string('Section');
            $table->string('SchoolYear');
            $table->string('Semester');
            $table->string('GradeLevel');
            $table->string('TrackStrand');
    
            // These lines must come before the foreign() declarations
            $table->unsignedBigInteger('Teacher_ID');
            $table->unsignedBigInteger('Coor_ID');
    
            // Now define foreign key constraints
            $table->foreign('Teacher_ID')->references('Teacher_ID')->on('teachers')->onDelete('cascade');
            $table->foreign('Coor_ID')->references('Coor_ID')->on('coordinators')->onDelete('cascade');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
