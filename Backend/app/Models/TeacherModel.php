<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class TeacherModel extends Authenticatable
{
    use HasApiTokens, Notifiable;

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

    protected $hidden = [
        'Password',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'Teacher_ID');
    }

    public function classes()
    {
        return $this->hasMany(Classes::class, 'Teacher_ID');
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

