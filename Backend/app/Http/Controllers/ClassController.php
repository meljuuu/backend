<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClassesModel;

class ClassController extends Controller
{
    public function getClassDetails($classId)
    {
        try {
            $classDetails = DB::table('classes as c')
                ->leftJoin('student_class as sc', 'c.Class_ID', '=', 'sc.Class_ID')
                ->leftJoin('students as s', 'sc.Student_ID', '=', 's.Student_ID')
                ->where('c.Class_ID', $classId)
                ->select(
                    'c.Class_ID',
                    'c.ClassName',
                    'c.Section',
                    'c.Grade_Level',
                    'c.Track',
                    'c.Curriculum',
                    DB::raw('COUNT(CASE WHEN s.Sex = "M" THEN 1 END) as maleCount'),
                    DB::raw('COUNT(CASE WHEN s.Sex = "F" THEN 1 END) as femaleCount'),
                    DB::raw('COUNT(DISTINCT s.Student_ID) as totalStudents')
                )
                ->groupBy(
                    'c.Class_ID',
                    'c.ClassName',
                    'c.Section',
                    'c.Grade_Level',
                    'c.Track',
                    'c.Curriculum'
                )
                ->first();

            if (!$classDetails) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'classId' => $classDetails->Class_ID,
                    'className' => $classDetails->ClassName,
                    'section' => $classDetails->Section,
                    'gradeLevel' => $classDetails->Grade_Level,
                    'track' => $classDetails->Track,
                    'curriculum' => $classDetails->Curriculum,
                    'maleCount' => (int)$classDetails->maleCount,
                    'femaleCount' => (int)$classDetails->femaleCount,
                    'totalStudents' => (int)$classDetails->totalStudents
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch class details: ' . $e->getMessage()
            ], 500);
        }
    }
}