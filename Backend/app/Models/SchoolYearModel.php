<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYearModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'SY_ID';

    public function classes()
    {
        return $this->hasMany(ClassesModel::class, 'SY_ID');
    }
}
