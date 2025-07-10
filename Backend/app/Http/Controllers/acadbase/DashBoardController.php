<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;
use App\Models\ClassesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function getStudentStats(): JsonResponse
    {
        try {
            // Get total students count
            $totalStudents = StudentModel::count();

            // Get students by curriculum
            $curriculumStats = StudentModel::select('Curriculum', DB::raw('count(*) as count'))
                ->groupBy('Curriculum')
                ->get()
                ->pluck('count', 'Curriculum')
                ->toArray();

            // Get students by track
            $trackStats = StudentModel::select('Track', DB::raw('count(*) as count'))
                ->groupBy('Track')
                ->get()
                ->pluck('count', 'Track')
                ->toArray();

            return response()->json([
                'total_students' => $totalStudents,
                'curriculum_stats' => $curriculumStats,
                'track_stats' => $trackStats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch student statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getLatestStudents(): JsonResponse
    {
        try {
            $latestStudents = StudentModel::orderBy('created_at', 'desc')
                ->limit(10)
                ->get([
                    'Student_ID',
                    'FirstName',
                    'LastName',
                    'Track',
                    'Curriculum',
                    'created_at as batch'
                ]);

            return response()->json($latestStudents);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch latest students',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getReleasedDocsStats(): JsonResponse
    {
        try {
            // Get released documents count by school year
            $releasedDocs = DB::table('acadbase')
                ->select('batch', DB::raw('count(*) as count'))
                ->where('status', 'Released')
                ->groupBy('batch')
                ->get()
                ->pluck('count', 'batch')
                ->toArray();

            // Get senior high and junior high counts
            $curriculumStats = DB::table('acadbase')
                ->select('curriculum', DB::raw('count(*) as count'))
                ->where('status', 'Released')
                ->groupBy('curriculum')
                ->get()
                ->pluck('count', 'curriculum')
                ->toArray();

            return response()->json([
                'released_docs' => $releasedDocs,
                'curriculum_stats' => $curriculumStats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch released documents statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentStatusCounts(): JsonResponse
    {
        try {
            $statusCounts = StudentModel::select('Status', DB::raw('count(*) as count'))
                ->groupBy('Status')
                ->get()
                ->pluck('count', 'Status')
                ->toArray();

            return response()->json([
                'status_counts' => $statusCounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch student status counts',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
}