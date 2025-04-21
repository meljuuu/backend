<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectGradeModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'Grade_ID';

    protected $fillable = [
        'Q1', 'Q2', 'Q3', 'Q4', 'FinalGrade', 'Remarks',
        'Student_ID', 'Teacher_ID', 'Coors_ID', 'Subject_ID'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'Student_ID');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'Subject_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'Teacher_ID');
    }
}
