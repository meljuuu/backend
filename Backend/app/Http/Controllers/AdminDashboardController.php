<?php

namespace App\Http\Controllers;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function getStudentCount()
    {
        $count = StudentModel::count();

        return response()->json([
            'total_students' => $count
        ]);
    }

    public function getTeacherCount()
    {
        $count = TeacherModel::count();

        return response()->json([
            'total_teachers' => $count,
        ]);
    }

     public function getStudentGenderDistribution(): JsonResponse
    {
   
        $counts = StudentModel::selectRaw('Curriculum, Sex, COUNT(*) as total')
            ->whereIn('Curriculum', ['JHS', 'SHS'])
            ->whereIn('Sex', ['M', 'F'])
            ->groupBy('Curriculum', 'Sex')
            ->get();

        $data = [
            'JHS_M' => 0,
            'JHS_F' => 0,
            'SHS_M' => 0,
            'SHS_F' => 0,
        ];

        foreach ($counts as $count) {
            $key = $count->Curriculum . '_' . $count->Sex;
            $data[$key] = $count->total;
        }

        return response()->json($data);
    }

    public function getStudentGradeDistribution(): JsonResponse
{
    // Grade levels stored as strings
    $gradeLevels = ['7', '8', '9', '10', '11', '12'];

    // Fetch and group count
    $counts = StudentModel::selectRaw('Grade_Level, COUNT(*) as total')
        ->whereIn('Grade_Level', $gradeLevels)
        ->groupBy('Grade_Level')
        ->get()
        ->keyBy('Grade_Level');

    // Format response
    $data = [];
    foreach ($gradeLevels as $grade) {
        $data["Grade $grade"] = $counts[$grade]->total ?? 0;
    }

    return response()->json($data);
}


}
