<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'Student_ID';

    public function grades()
    {
        return $this->hasMany(SubjectGrade::class, 'Student_ID');
    }

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class, 'Student_ID');
    }
}
