<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;
use App\Models\ClassesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\acadbase\MasterlistModel;

class acadDashboardController extends Controller
{
    public function acadlist(): JsonResponse
{
    try {
        // Group by SY_Year > Curriculum > Track
        $results = MasterlistModel::selectRaw('
                acadbase.curriculum,
                acadbase.batch,
                acadbase.track,
                COUNT(*) as total
            ')
            ->groupBy('acadbase.batch', 'acadbase.curriculum', 'acadbase.track')
            ->orderBy('acadbase.batch', 'desc')
            ->get();

        $organizedStats = [];

        foreach ($results as $row) {
            $year = $row->batch;
            $curriculum = $row->curriculum ?? 'Unknown';
            $track = $row->track ?? 'Unknown';

            $organizedStats[$year][$curriculum][$track] = $row->total;
        }

        return response()->json([
            'track_stats' => $organizedStats
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
 public function acadbaseGenderDistribution(Request $request): JsonResponse
    {
   
        $curriculum = $request->input('curriculum'); // e.g. "JHS" or "SHS"

        $query = \App\Models\acadbase\MasterlistModel::selectRaw('curriculum, gender, COUNT(*) as total')
            ->whereIn('curriculum', ['JHS', 'SHS'])
            ->whereIn('gender', ['M', 'F']);

        if ($curriculum) {
            $query->where('curriculum', $curriculum);
        }

        $counts = $query->groupBy('curriculum', 'gender')->get();

        $data = [
            'JHS_Male' => 0,
            'JHS_Female' => 0,
            'SHS_Male' => 0,
            'SHS_Female' => 0,
        ];

        foreach ($counts as $count) {
            $genderLabel = $count->gender === 'M' ? 'Male' : 'Female';
            $key = $count->curriculum . '_' . $genderLabel;
            $data[$key] = $count->total;
        }

        return response()->json($data);
    }
    
    public function getGenderDistributionYears(): JsonResponse
    {
        $years = MasterlistModel::select('Batch')
            ->distinct()
            ->orderBy('Batch', 'desc')
            ->pluck('Batch');
        return response()->json($years);
    }
}