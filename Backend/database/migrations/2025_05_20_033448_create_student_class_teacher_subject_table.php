<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_class_teacher_subject', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_class_id');
            $table->unsignedBigInteger('teacher_subject_id');

            $table->timestamps();

            $table->foreign('student_class_id')
                ->references('StudentClass_ID')->on('student_class')
                ->onDelete('cascade');

            $table->foreign('teacher_subject_id')
                ->references('id')->on('teachers_subject')
                ->onDelete('cascade');

            // Use shorter name for the unique index
            $table->unique(['student_class_id', 'teacher_subject_id'], 'sc_ts_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_teacher_subject');
    }
};
