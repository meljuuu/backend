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

    // Enable auto-incrementing primary key
    public $incrementing = true;

    // Primary key is an integer
    protected $keyType = 'int';

    // Fillable fields for mass assignment
    protected $fillable = [
        'SubjectName',
        'SubjectCode',
    ];
}
