<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherModel extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    protected $primaryKey = 'Teacher_ID';

    protected $fillable = [
        'Teacher_ID', 
        'Email',
        'Password',
        'FirstName',
        'LastName',
        'MiddleName',
        'BirthDate',
        'Sex',
        'Position',
        'ContactNumber',
        'Address',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'Teacher_ID');
    }

    public function classes()
    {
        return $this->hasMany(Classes::class, 'Teacher_ID');
    }
}
