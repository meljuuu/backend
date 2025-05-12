<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Research extends Model
{
    protected $table = 'research';
    protected $primaryKey = 'Research_ID';
    
    protected $fillable = [
        'Research_ID',
        'Teacher_ID',
        'Title',
        'Abstract',
        'created_at'
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID');
    }
}