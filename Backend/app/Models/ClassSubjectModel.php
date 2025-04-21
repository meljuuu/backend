<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubjectModel extends Model
{
    use HasFactory;

    protected $table = 'class_subject_table__junction';
    protected $primaryKey = 'StudentClassSub_ID';

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'Subject_ID');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'Class_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'Teacher_ID');
    }
    
}
