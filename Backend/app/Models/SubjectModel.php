<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectModel extends Model
{
    use HasFactory;

    // Set the table name explicitly (optional if Laravel auto-detects)
    protected $table = 'subjects';

    // Set the primary key
    protected $primaryKey = 'Subject_ID';

    protected $fillable = [
        'SubjectName', 
        'SubjectCode', 
        'GradeLevel', 
    ];

    public function grades()
    {
        return $this->hasMany(SubjectGradeModel::class, 'Subject_ID');
    }

    public function teachers()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID');
        return $this->belongsToMany(TeacherModel::class, 'teachers_subject', 'subject_id', 'teacher_id')
                    ->withPivot('subject_code')
                    ->withTimestamps();
    }
}
