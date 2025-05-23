<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Research;
use App\Models\SubjectModel;
use App\Models\TeachersSubject;
use App\Models\ClassesModel;
use App\Models\StudentClassModel;

class TeacherModel extends Authenticatable
{
    use HasApiTokens, Notifiable;

    

    protected $table = 'teachers';
    protected $primaryKey = 'Teacher_ID';

    protected $fillable = [
        'Teacher_ID', 
        'EmployeeNo',
        'Email',
        'Password',
        'FirstName',
        'LastName',
        'MiddleName',
        'BirthDate',
        'Suffix',
        'Educational_Attainment',
        'Teaching_Position',
        'Sex',
        'Position',
        'ContactNumber',
        'Address',
        'Research',
        'Avatar',
    ];

    protected $hidden = [
        'Password',
    ];

    public function subjects()
    {
        return $this->belongsToMany(SubjectModel::class, 'teachers_subject', 'teacher_id', 'subject_id')
                    ->withPivot('subject_code')
                    ->withTimestamps();
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeachersSubject::class, 'teacher_id', 'Teacher_ID');
    }

    public function teachingClasses()
    {
        return $this->hasManyThrough(
            ClassesModel::class,
            StudentClassModel::class,
            'Teacher_ID',
            'Class_ID',
            'Teacher_ID',
            'Class_ID'
        )->distinct();
    }

    public function classes()
    {
        // return $this->hasMany(ClassesModel::class, 'Teacher_ID');
        return $this->belongsToMany(ClassesModel::class, 'student_class', 'Teacher_ID', 'Class_ID')
                ->withPivot('isAdvisory');
    }

    public function researches()
    {
        return $this->hasMany(Research::class, 'Teacher_ID');
    }

    public function lessonPlans()
    {
        return $this->hasMany(LessonPlan::class, 'Teacher_ID');
    }

    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function getEmailForPasswordReset()
    {
        return $this->Email;
    }
}

