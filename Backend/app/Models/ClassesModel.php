<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassesModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'Class_ID';

    protected $fillable = [
        'ClassName',
        'Section',
        'SY_ID',
        'Grade_Level',
        'Track',
        'Teacher_ID',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'Grade_Level' => 'string',
    ];

    public function students()
    {
        return $this->belongsToMany(StudentModel::class, 'student_class', 'Class_ID', 'Student_ID')
            ->withPivot(['SY_ID', 'Teacher_ID', 'isAdvisory'])
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
}
