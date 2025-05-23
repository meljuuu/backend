<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPlan extends Model
{
    use HasFactory;

    protected $primaryKey = 'LessonPlan_ID';
    protected $fillable = [
        'Teacher_ID',
        'lesson_plan_no',
        'grade_level',
        'section',
        'category',
        'link',
        'status',
        'comments'
    ];

    protected $casts = [
        'lesson_plan_no' => 'string',
        'grade_level' => 'string',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID');
    }

    // Lesson Plan Don't Delete!
    public function getTeacherNameAttribute()
    {
        $teacher = $this->teacher;
        if (!$teacher) return null;

        $middle = $teacher->MiddleName ? ' ' . $teacher->MiddleName . ' ' : ' ';
        return $teacher->FirstName . $middle . $teacher->LastName;
    }

}