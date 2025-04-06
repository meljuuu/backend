<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';
    protected $primaryKey = 'Class_ID';
    protected $fillable = [
        'ClassName', 'Section', 'SchoolYear', 'Semester',
        'GradeLevel', 'TrackStrand', 'Teacher_ID', 'Coor_ID'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'Teacher_ID');
    }

    public function coordinator()
    {
        return $this->belongsTo(Coordinator::class, 'Coor_ID');
    }
}
