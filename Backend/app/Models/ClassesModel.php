<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassesModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'Class_ID';

    public $incrementing = true;

    protected $keyType = 'int';

    // Which columns are mass assignable
    protected $fillable = [
        'ClassName',
        'Section',
        'SY_ID',
        'Grade_Level',
        'Status',
        'Track',
        'Curriculum',
        'comments',
    ];

    protected $casts = [
        'Grade_Level' => 'string',
    ];

    public function students()
    {
        return $this->belongsToMany(StudentModel::class, 'student_class', 'Class_ID', 'Student_ID')
            ->withPivot(['SY_ID', 'Adviser_ID', 'isAdvisory'])
            ->withTimestamps();
    }

    public function subjects()
    {
        return $this->belongsToMany(SubjectModel::class, 'class_subject', 'Class_ID', 'Subject_ID')
            ->withPivot(['Student_ID', 'SY_ID', 'Teacher_ID'])
            ->withTimestamps();
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID', 'Teacher_ID');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYearModel::class, 'SY_ID', 'SY_ID');
    }


    public function studentClasses()
    {
        return $this->hasMany(StudentClassModel::class, 'Class_ID', 'Class_ID');
    }


    public function classSubjects()
    {
        return $this->hasMany(ClassSubjectModel::class, 'Class_ID', 'Class_ID');
    }

    public function adviser()
    {
        return $this->belongsTo(TeacherModel::class, 'Adviser_ID', 'Teacher_ID');
    }

    public function teacherSubjects()
    {
        return $this->hasManyThrough(
            TeachersSubject::class,
            StudentClassModel::class,
            'Class_ID',
            'id',
            'Class_ID',
            'teacher_subject_id'
        )->distinct();
    }
}
