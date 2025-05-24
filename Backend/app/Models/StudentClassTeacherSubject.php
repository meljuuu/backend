<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClassTeacherSubject extends Model
{
    protected $table = 'student_class_teacher_subject';

    protected $fillable = [
        'student_class_id',
        'teacher_subject_id'
    ];

    public function studentClass()
    {
        return $this->belongsTo(StudentClassModel::class, 'student_class_id', 'StudentClass_ID');
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeachersSubject::class, 'teacher_subject_id');
    }

    // Add this to get the class through student class
    public function class()
    {
        return $this->hasOneThrough(
            ClassesModel::class,
            StudentClassModel::class,
            'StudentClass_ID',
            'Class_ID',
            'student_class_id',
            'Class_ID'
        );
    }

    // Add this to get the teacher through teacher subject
    public function teacher()
    {
        return $this->hasOneThrough(
            TeacherModel::class,
            TeachersSubject::class,
            'id',
            'Teacher_ID',
            'teacher_subject_id',
            'teacher_id'
        );
    }

    // Add this relationship to get grades
    public function grades()
    {
        return $this->hasMany(SubjectGradeModel::class, 'Subject_ID', 'teacher_subject_id')
            ->whereHas('teacherSubject', function($query) {
                $query->where('teacher_subject_id', $this->teacher_subject_id);
            });
    }
}