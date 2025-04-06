<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $primaryKey = 'Subject_ID';
    protected $fillable = [
        'SubjectName', 'SubjectCode', 'Student_ID', 'Teacher_ID', 'Coor_ID'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'Student_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'Teacher_ID');
    }

    public function coordinator()
    {
        return $this->belongsTo(Coordinator::class, 'Coor_ID');
    }

    public function subjectGrades()
    {
        return $this->hasMany(SubjectGrade::class, 'Subject_ID');
    }
}

