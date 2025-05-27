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
        'faculty_name',
        'pdf_storage'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
