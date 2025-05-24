<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TeacherModel;
use App\Models\SubjectModel;
use App\Models\ClassesModel;
use App\Models\SchoolYearModel;
use App\Models\TeachersSubject;

class StudentClassModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_class';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'StudentClass_ID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Student_ID',
        'Class_ID',
        'ClassName',
        'SY_ID',
        'Adviser_ID',
        'isAdvisory',
        'Status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isAdvisory' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    public function student()
    {
        return $this->belongsTo(StudentModel::class, 'Student_ID', 'Student_ID');
    }

    public function class()
    {
        return $this->belongsTo(ClassesModel::class, 'Class_ID', 'Class_ID');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYearModel::class, 'SY_ID', 'SY_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID', 'Teacher_ID');
    }

    public function adviser()
    {
        return $this->belongsTo(TeacherModel::class, 'Adviser_ID', 'Teacher_ID');
    }

    public function teacherSubjects()
    {
        return $this->belongsToMany(
            TeachersSubject::class,
            'student_class_teacher_subject',
            'student_class_id',
            'teacher_subject_id'
        )->with(['subject', 'teacher']);
    }

    public function subjects()
    {
        return $this->hasManyThrough(
            SubjectModel::class,
            TeachersSubject::class,
            'id',
            'Subject_ID',
            'teacher_subject_id',
            'subject_id'
        )->distinct();
    }
}
