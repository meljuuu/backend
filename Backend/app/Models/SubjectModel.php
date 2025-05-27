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
        // Many-to-many relationship through pivot table teachers_subject
        return $this->belongsToMany(TeacherModel::class, 'teachers_subject', 'subject_id', 'teacher_id')
                    ->withPivot('subject_code')
                    ->withTimestamps();
    }

    public function teacherSubjects() {
        // One-to-many to the pivot model for detailed pivot info if needed
        return $this->hasMany(TeachersSubject::class, 'subject_id', 'Subject_ID');
    }
}
