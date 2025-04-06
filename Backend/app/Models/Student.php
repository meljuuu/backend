<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'Student_ID';
    protected $fillable = [
        'LRN', 'Email', 'Password', 'FirstName', 'LastName', 'MiddleName',
        'BirthDate', 'Sex', 'ContactNumber', 'Address', 'Guardian', 'Curriculum'
    ];

    public function subjectGrades()
    {
        return $this->hasMany(SubjectGrade::class, 'Student_ID');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'Student_ID');
    }
}
