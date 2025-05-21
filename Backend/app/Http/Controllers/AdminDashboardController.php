<?php

namespace App\Http\Controllers;
use App\Models\StudentModel;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function getStudentCount()
{
    $count = StudentModel::count();

    return response()->json([
        'total_students' => $count
    ]);
}

}
