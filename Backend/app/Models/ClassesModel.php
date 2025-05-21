<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassesModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $primaryKey = 'Class_ID';

    public $incrementing = true;

    protected $keyType = 'int';

    // Which columns are mass assignable
    protected $fillable = [
        'ClassName',
        'Section',
        'SY_ID',
        'Grade_Level',
        'Status',
        'Track',
        'Curriculum',
        'comments',
    ];

    /**
     * Define relationship to SchoolYear model
     */
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'SY_ID', 'SY_ID');
    }

    public function studentClasses()
    {
        return $this->hasMany(StudentClassModel::class, 'Class_ID', 'Class_ID');
    }
}
