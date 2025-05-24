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

class DashboardController extends Controller
{
    public function getAdvisoryStats(Request $request)
    {
        $teacher = $request->user();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
<<<<<<< HEAD
            // Get advisory class where this teacher is the adviser
            $advisoryClass = DB::table('student_class')
                ->where('Adviser_ID', $teacher->Teacher_ID)
                ->where('isAdvisory', true)
                ->first();

=======
            // Fetch the single advisory class where this teacher is the adviser
            $advisoryClass = ClassesModel::where('Adviser_ID', $teacher->Teacher_ID)
                ->first();

            // If no advisory class found, return zeros
>>>>>>> 08dcd437cc9705d665006fbc55ff1ee00eac979c
            if (!$advisoryClass) {
                return response()->json([
                    'advisoryClass' => null,
                    'totalStudents' => 0,
                    'maleCount' => 0,
                    'femaleCount' => 0,
                ]);
            }

<<<<<<< HEAD
            // Get student counts with gender information
            $studentCounts = DB::table('student_class')
                ->join('students', 'student_class.Student_ID', '=', 'students.Student_ID')
                ->where('student_class.Class_ID', $advisoryClass->Class_ID)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN students.Sex = "M" THEN 1 ELSE 0 END) as male_count'),
                    DB::raw('SUM(CASE WHEN students.Sex = "F" THEN 1 ELSE 0 END) as female_count')
                )
                ->first();

            return response()->json([
                'advisoryClass' => $advisoryClass,
                'totalStudents' => $studentCounts->total ?? 0,
                'maleCount' => $studentCounts->male_count ?? 0,
                'femaleCount' => $studentCounts->female_count ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching advisory stats: ' . $e->getMessage());
=======
            // Get counts from student_class table
            $students = DB::table('student_class')
                ->join('students', 'student_class.Student_ID', '=', 'students.Student_ID')
                ->where('student_class.Class_ID', $advisoryClass->Class_ID)
                ->get();

            // Count males and females
            $maleCount = 0;
            $femaleCount = 0;
            foreach ($students as $student) {
                if ($student->Sex == 'M') {
                    $maleCount++;
                } else if ($student->Sex == 'F') {
                    $femaleCount++;
                }
            }

            return response()->json([
                'advisoryClass' => $advisoryClass,
                'totalStudents' => $students->count(),
                'maleCount' => $maleCount,
                'femaleCount' => $femaleCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching advisory stats: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
>>>>>>> 08dcd437cc9705d665006fbc55ff1ee00eac979c
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getSubjectClasses(Request $request)
<<<<<<< HEAD
    {
        $teacher = $request->user();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
            // Get all classes and subjects for this teacher
            $subjectClasses = DB::table('student_class_teacher_subject')
                ->join('student_class', 'student_class_teacher_subject.student_class_id', '=', 'student_class.StudentClass_ID')
                ->join('teachers_subject', 'student_class_teacher_subject.teacher_subject_id', '=', 'teachers_subject.id')
                ->join('subjects', 'teachers_subject.subject_id', '=', 'subjects.Subject_ID')
                ->join('classes', 'student_class.Class_ID', '=', 'classes.Class_ID')
                ->where('teachers_subject.teacher_id', $teacher->Teacher_ID)
                ->select(
                    'classes.ClassName',
                    'classes.Grade_Level',
                    'subjects.SubjectName',
                    DB::raw('COUNT(DISTINCT student_class.Student_ID) as student_count')
                )
                ->groupBy('classes.ClassName', 'classes.Grade_Level', 'subjects.SubjectName')
                ->get();

            return response()->json($subjectClasses);
        } catch (\Exception $e) {
            Log::error('Error fetching subject classes: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }
=======
{
    $teacher = $request->user();

    if (!$teacher) {
        return response()->json(['error' => 'Teacher not found'], 404);
    }

    try {
        // Get subject classes from teachers_subject table
        $subjectClasses = [];
        
        // First, try to get classes where this teacher is assigned
        $classes = DB::table('classes')
            ->where('Adviser_ID', $teacher->Teacher_ID)
            ->get();
        
        foreach ($classes as $class) {
            // Get subjects for this class
            $subjects = DB::table('class_subject')
                ->join('subjects', 'class_subject.Subject_ID', '=', 'subjects.Subject_ID')
                ->where('class_subject.Class_ID', $class->Class_ID)
                ->get();

            if ($subjects->isEmpty()) {
                // If no subjects found, still add class with "General" subject
                $studentCount = DB::table('student_class')
                    ->where('Class_ID', $class->Class_ID)
                    ->count();
                    
                $subjectClasses[] = [
                    'name' => $class->ClassName ?? 'Grade ' . $class->Grade . ' - ' . $class->Section ?? 'Class ' . $class->Class_ID,
                    'subject' => 'General', // Default subject name
                    'count' => $studentCount
                ];
            } else {
                // Add each subject for this class
                foreach ($subjects as $subject) {
                    $studentCount = DB::table('student_class')
                        ->where('Class_ID', $class->Class_ID)
                        ->count();
                        
                    $subjectClasses[] = [
                        'name' => $class->ClassName ?? 'Grade ' . $class->Grade . ' - ' . $class->Section ?? 'Class ' . $class->Class_ID,
                        'subject' => $subject->SubjectName ?? $subject->Subject_Name ?? 'Subject ' . $subject->Subject_ID,
                        'count' => $studentCount
                    ];
                }
            }
        }

        // If still empty, check teachers_subject table directly
        if (empty($subjectClasses)) {
            $teacherSubjects = DB::table('teachers_subject')
                ->join('subjects', 'teachers_subject.subject_id', '=', 'subjects.Subject_ID')
                ->where('teachers_subject.teacher_id', $teacher->Teacher_ID)
                ->get();
                
            foreach ($teacherSubjects as $subject) {
                // Find classes for this subject via class_subject table
                $classSubjects = DB::table('class_subject')
                    ->join('classes', 'class_subject.Class_ID', '=', 'classes.Class_ID')
                    ->where('class_subject.Subject_ID', $subject->Subject_ID)
                    ->get();
                    
                foreach ($classSubjects as $classSubject) {
                    // Count students in this class
                    $studentCount = DB::table('student_class')
                        ->where('Class_ID', $classSubject->Class_ID)
                        ->count();
                        
                    $subjectClasses[] = [
                        'name' => $classSubject->ClassName ?? 'Class ' . $classSubject->Class_ID,
                        'subject' => $subject->SubjectName ?? $subject->Subject_Name ?? 'Subject ' . $subject->Subject_ID,
                        'count' => $studentCount
                    ];
                }
            }
        }

        // Log the final data to help with debugging
        Log::info('Subject classes data: ' . json_encode($subjectClasses));

        return response()->json($subjectClasses);
    } catch (\Exception $e) {
        Log::error('Error fetching subject classes: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        return response()->json([], 200); // Return empty array to prevent frontend errors
    }
}
>>>>>>> 08dcd437cc9705d665006fbc55ff1ee00eac979c

    public function getGradeSummary(Request $request)
    {
        $teacher = $request->user();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

<<<<<<< HEAD
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
=======
        // Initialize counters for each grade range
        $gradeSummary = [
            '90-100' => 0,
            '85-89' => 0,
            '80-84' => 0, 
            '75-79' => 0,
            'Below 75' => 0
        ];

        try {
            // Get all grades for this teacher directly from subject_grades table
            $grades = DB::table('subject_grades')
                ->where('Teacher_ID', $teacher->Teacher_ID)
                ->whereNotNull('FinalGrade')
                ->get(['FinalGrade']);
                
            // Count grades in each range
            foreach ($grades as $grade) {
                $numericGrade = floatval($grade->FinalGrade);
                
                if ($numericGrade >= 90) {
                    $gradeSummary['90-100']++;
                } elseif ($numericGrade >= 85) {
                    $gradeSummary['85-89']++;
                } elseif ($numericGrade >= 80) {
                    $gradeSummary['80-84']++;
                } elseif ($numericGrade >= 75) {
                    $gradeSummary['75-79']++;
                } else {
                    $gradeSummary['Below 75']++;
                }
            }
            
            return response()->json($gradeSummary);
        } catch (\Exception $e) {
            Log::error('Error fetching grade summary: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Return the empty grade summary rather than an error
            return response()->json($gradeSummary);
>>>>>>> 08dcd437cc9705d665006fbc55ff1ee00eac979c
        }
    }

    public function getRecentGrades(Request $request)
    {
        $teacher = $request->user();

        if (!$teacher) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
<<<<<<< HEAD
            // Get recent grades with student and subject information
            $recentGrades = DB::table('subject_grades')
=======
            // Get recent grades directly from subject_grades table
            $grades = DB::table('subject_grades')
>>>>>>> 08dcd437cc9705d665006fbc55ff1ee00eac979c
                ->join('students', 'subject_grades.Student_ID', '=', 'students.Student_ID')
                ->join('subjects', 'subject_grades.Subject_ID', '=', 'subjects.Subject_ID')
                ->where('subject_grades.Teacher_ID', $teacher->Teacher_ID)
                ->whereNotNull('subject_grades.FinalGrade')
                ->select(
<<<<<<< HEAD
                    DB::raw('CONCAT(students.FirstName, " ", students.LastName) as student_name'),
                    'subjects.SubjectName',
                    'subject_grades.FinalGrade',
                    'subject_grades.Status',
=======
                    'students.FirstName', 
                    'students.LastName',
                    'subjects.SubjectName',
                    'subject_grades.FinalGrade',
>>>>>>> 08dcd437cc9705d665006fbc55ff1ee00eac979c
                    'subject_grades.updated_at'
                )
                ->orderBy('subject_grades.updated_at', 'desc')
                ->limit(10)
<<<<<<< HEAD
                ->get()
                ->map(function ($grade) {
                    return [
                        'student' => $grade->student_name,
                        'subject' => $grade->SubjectName,
                        'grade' => $grade->FinalGrade,
                        'status' => $grade->Status,
                        'date' => date('M d, Y', strtotime($grade->updated_at))
                    ];
                });

            return response()->json($recentGrades);
        } catch (\Exception $e) {
            Log::error('Error fetching recent grades: ' . $e->getMessage());
=======
                ->get();
            
            // Format the data for frontend
            $recentGrades = [];
            foreach ($grades as $grade) {
                $recentGrades[] = [
                    'student' => $grade->FirstName . ' ' . $grade->LastName,
                    'subject' => $grade->SubjectName ?? 'Unknown Subject',
                    'grade' => floatval($grade->FinalGrade),
                    'date' => date('M d, Y', strtotime($grade->updated_at ?? now()))
                ];
            }
            
            return response()->json($recentGrades);
        } catch (\Exception $e) {
            Log::error('Error fetching recent grades: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Return an empty array on error
>>>>>>> 08dcd437cc9705d665006fbc55ff1ee00eac979c
            return response()->json([]);
        }
    }
}