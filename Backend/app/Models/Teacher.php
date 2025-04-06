<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $primaryKey = 'Teacher_ID';
    protected $fillable = [
        'Email', 'Password', 'FirstName', 'LastName', 'MiddleName',
        'BirthDate', 'Sex', 'ContactNumber', 'Address'
    ];

    public function classes()
    {
        return $this->hasMany(Classes::class, 'Teacher_ID');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'Teacher_ID');
    }

    public function subjectGrades()
    {
        return $this->hasMany(SubjectGrade::class, 'Teacher_ID');
    }
}
