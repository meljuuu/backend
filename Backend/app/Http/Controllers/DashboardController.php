<?php

namespace App\Http\Controllers;

use App\Models\ClassesModel;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\SubjectModel;
use App\Models\SubjectGradeModel;
use App\Models\StudentClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function getAdvisoryStats(Request $request)
    {
        try {
            $teacherId = $request->user()->Teacher_ID;

            // Get advisory classes for this teacher
            $advisoryClasses = DB::table('student_class')
                ->join('students', 'student_class.Student_ID', '=', 'students.Student_ID')
                ->where('student_class.Adviser_ID', $teacherId)
                ->where('student_class.isAdvisory', true)
                ->select(
                    'students.Student_ID',
                    'students.FirstName',
                    'students.LastName',
                    'students.Sex',
                    'student_class.ClassName'
                )
                ->get();

            // Count total students and gender distribution
            $totalStudents = $advisoryClasses->count();
            $maleCount = $advisoryClasses->where('Sex', 'M')->count();
            $femaleCount = $advisoryClasses->where('Sex', 'F')->count();

            return response()->json([
                'totalStudents' => $totalStudents,
                'maleCount' => $maleCount,
                'femaleCount' => $femaleCount,
                'subjectClasses' => $advisoryClasses
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching advisory classes: ' . $e->getMessage());
            return response()->json([
                'totalStudents' => 0,
                'maleCount' => 0,
                'femaleCount' => 0,
                'subjectClasses' => []
            ]);
        }
    }

    public function getSubjectClasses(Request $request)
    {
        $teacher = $request->user();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
            // Get all classes and subjects for this teacher where isAdvisory is false
            $subjectClasses = DB::table('student_class')
                ->join('student_class_teacher_subject', 'student_class.StudentClass_ID', '=', 'student_class_teacher_subject.student_class_id')
                ->join('teachers_subject', 'student_class_teacher_subject.teacher_subject_id', '=', 'teachers_subject.id')
                ->join('subjects', 'teachers_subject.subject_id', '=', 'subjects.Subject_ID')
                ->join('classes', 'student_class.Class_ID', '=', 'classes.Class_ID')
                ->where('teachers_subject.teacher_id', $teacher->Teacher_ID)
                ->where('student_class.isAdvisory', false)
                ->select(
                    'classes.ClassName',
                    'classes.Grade_Level',
                    'subjects.SubjectName',
                    'student_class.isAdvisory',
                    DB::raw('COUNT(DISTINCT student_class.Student_ID) as student_count')
                )
                ->groupBy('classes.ClassName', 'classes.Grade_Level', 'subjects.SubjectName', 'student_class.isAdvisory')
                ->get();

            return response()->json($subjectClasses);
        } catch (\Exception $e) {
            Log::error('Error fetching subject classes: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }

    public function getGradeSummary(Request $request)
    {
        $teacher = $request->user();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
            // Get grade distribution for this teacher's students
            $gradeSummary = DB::table('subject_grades')
                ->where('Teacher_ID', $teacher->Teacher_ID)
                ->whereNotNull('FinalGrade')
                ->select(
                    DB::raw('CASE 
                        WHEN FinalGrade >= 90 THEN "90-100"
                        WHEN FinalGrade >= 85 THEN "85-89"
                        WHEN FinalGrade >= 80 THEN "80-84"
                        WHEN FinalGrade >= 75 THEN "75-79"
                        ELSE "Below 75"
                    END as grade_range'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('grade_range')
                ->pluck('count', 'grade_range')
                ->toArray();

            // Ensure all ranges are present
            $ranges = ['90-100', '85-89', '80-84', '75-79', 'Below 75'];
            $summary = array_fill_keys($ranges, 0);
            $summary = array_merge($summary, $gradeSummary);

            return response()->json($summary);
        } catch (\Exception $e) {
            Log::error('Error fetching grade summary: ' . $e->getMessage());
            return response()->json([
                '90-100' => 0,
                '85-89' => 0,
                '80-84' => 0,
                '75-79' => 0,
                'Below 75' => 0
            ]);
        }
    }

    public function getRecentGrades(Request $request)
    {
        $teacher = $request->user();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
            // Get recent grades with student and subject information
            $recentGrades = DB::table('subject_grades')
                ->join('students', 'subject_grades.Student_ID', '=', 'students.Student_ID')
                ->join('subjects', 'subject_grades.Subject_ID', '=', 'subjects.Subject_ID')
                ->leftJoin('student_class', function($join) use ($teacher) {
                    $join->on('students.Student_ID', '=', 'student_class.Student_ID')
                        ->where('student_class.Adviser_ID', '=', $teacher->Teacher_ID)
                        ->where('student_class.isAdvisory', '=', true);
                })
                ->where('subject_grades.Teacher_ID', $teacher->Teacher_ID)
                ->whereNotNull('subject_grades.FinalGrade')
                ->select(
                    DB::raw('CONCAT(students.FirstName, " ", students.LastName) as student_name'),
                    'subjects.SubjectName',
                    'subject_grades.FinalGrade',
                    'subject_grades.Status',
                    'subject_grades.updated_at',
                    DB::raw('CASE WHEN student_class.StudentClass_ID IS NOT NULL THEN true ELSE false END as is_advisory')
                )
                ->orderBy('subject_grades.updated_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($grade) {
                    return [
                        'student' => $grade->student_name,
                        'subject' => $grade->SubjectName,
                        'grade' => $grade->FinalGrade,
                        'status' => $grade->Status,
                        'date' => date('M d, Y', strtotime($grade->updated_at)),
                        'isAdvisory' => (bool)$grade->is_advisory
                    ];
                });

            return response()->json($recentGrades);
        } catch (\Exception $e) {
            Log::error('Error fetching recent grades: ' . $e->getMessage());
            return response()->json([]);
        }
    }

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

    public function getRecentAddedStudents(): JsonResponse
    {
        try {
            $recentStudents = StudentModel::orderBy('created_at', 'desc')
                ->limit(10)
                ->get([
                    'Student_ID',
                    'FirstName',
                    'LastName',
                    'Track',
                    'Curriculum',
                    'created_at as batch'
                ]);

            return response()->json($recentStudents);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch recent students',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getRecentReleasedStudents(): JsonResponse
    {
        try {
            $releasedStudents = DB::table('acadbase')
                ->where('status', 'Released')
                ->orderBy('furnished_date', 'desc')
                ->limit(10)
                ->get([
                    'id as Student_ID',
                    'name',
                    'track as Track',
                    'batch',
                    'furnished_date'
                ]);

            return response()->json($releasedStudents);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch released students',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}