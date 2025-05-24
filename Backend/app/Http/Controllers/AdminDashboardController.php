<?php

namespace App\Http\Controllers;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\ClassesModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

  public function countAcceptedClasses()
    {
        try {
            $count = ClassesModel::where('Status', 'Accepted')->count();

            return response()->json([
                'accepted_classes_count' => $count
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to count accepted classes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getLatestUpdatedStudents(): JsonResponse
    {
        try {
            $latestStudents = StudentModel::orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json($latestStudents);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch latest students.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

      public function getSubmissionStatusCounts()
    {
        $statuses = ['Accepted', 'Pending', 'Declined'];

        $results = StudentModel::whereIn('Status', $statuses)
            ->selectRaw('Status, Sex, COUNT(*) as count')
            ->groupBy('Status', 'Sex')
            ->get();

        // Structure result like: { "Male": { Accepted: 10, Pending: 5, Declined: 2 }, "Female": { ... } }
        $formatted = [
            'Male' => ['Accepted' => 0, 'Pending' => 0, 'Declined' => 0],
            'Female' => ['Accepted' => 0, 'Pending' => 0, 'Declined' => 0],
        ];

        foreach ($results as $row) {
            $sex = $row->Sex === 'M' ? 'Male' : 'Female';
            $formatted[$sex][$row->Status] = $row->count;
        }

        return response()->json($formatted);
    }

   public function getAcceptedStudentsPerGrade()
    {
        $grades = ['7', '8', '9', '10', '11', '12'];

        $results = StudentModel::select('Grade_Level', DB::raw('count(*) as count'))
            ->where('Status', 'Accepted')
            ->whereIn('Grade_Level', $grades)
            ->groupBy('Grade_Level')
            ->get()
            ->keyBy('Grade_Level');

        // Build the complete list with default count = 0
        $finalResults = collect($grades)->map(function ($grade) use ($results) {
            return [
                'Grade_Level' => $grade,
                'count' => $results->has($grade) ? $results[$grade]->count : 0,
            ];
        });

        return response()->json($finalResults);
    }

    public function countPendingClasses()
    {
        $pendingCount = ClassesModel::where('Status', 'Pending')->count();

        return response()->json([
            'pending_classes_count' => $pendingCount
        ]);
    }


}
