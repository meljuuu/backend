<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassesModel extends Model
{
    use HasFactory;

    protected $table = 'class_table';
    protected $primaryKey = 'Class_ID';

    public function students()
    {
        return $this->hasMany(StudentClass::class, 'Class_ID');
    }

    public function subjects()
    {
        return $this->hasMany(ClassSubject::class, 'Class_ID');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'SY_ID');
    }
}
