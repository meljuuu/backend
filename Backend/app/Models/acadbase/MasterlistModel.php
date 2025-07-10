<?php

namespace App\Models\acadbase;

use Illuminate\Database\Eloquent\Model;

class MasterlistModel extends Model
{
    protected $table = 'acadbase';
    
    protected $fillable = [
        'lrn',
        'name',
        'track',
        'batch',
        'curriculum',
        'status',
        'gender',
        'faculty_name',
        'pdf_storage',
        'birthdate'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
