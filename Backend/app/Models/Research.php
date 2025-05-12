<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Research extends Model
{
    protected $table = 'research';
    protected $primaryKey = 'Research_ID';
    
    protected $fillable = [
        'Teacher_ID',
        'Title',
        'Abstract'
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID');
    }
}