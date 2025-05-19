<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesModel;
use App\Models\User;
use App\Models\Teacher;
use App\Models\SchoolYearModel;
use App\Models\StudentModel;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{

    public function getAllWithStudentCount()
    {
        return ClassesModel::select(
                'classes.*',
                DB::raw('(SELECT COUNT(*) FROM students WHERE students.Track = classes.Track) as student_added')
            )
            ->with(['adviser', 'schoolYear'])
            ->get();
    }

}
