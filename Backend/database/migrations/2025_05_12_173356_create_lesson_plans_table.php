<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id('LessonPlan_ID');
            $table->foreignId('Teacher_ID')->constrained('teachers', 'Teacher_ID');
            $table->string('lesson_plan_no');
            $table->string('grade_level');
            $table->string('section');
            $table->string('category');
            $table->string('link');
            $table->enum('status', ['Pending', 'Approved', 'Declined'])->default('Pending');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_plans');
    }
};