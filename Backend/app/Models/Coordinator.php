<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinator extends Model
{
    protected $primaryKey = 'Coor_ID';
    protected $fillable = [
        'Email', 'Password', 'FirstName', 'LastName', 'MiddleName',
        'BirthDate', 'Sex', 'ContactNumber', 'Address'
    ];

    public function classes()
    {
        return $this->hasMany(Classes::class, 'Coor_ID');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'Coor_ID');
    }

    public function subjectGrades()
    {
        return $this->hasMany(SubjectGrade::class, 'Coor_ID');
    }
}