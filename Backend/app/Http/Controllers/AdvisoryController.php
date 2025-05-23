<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;
use App\Models\StudentClassModel;
use App\Models\TeachersSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\StudentClassTeacherSubject;
use App\Models\SubjectGradeModel;
use Illuminate\Support\Facades\DB;

class AdvisoryController extends Controller
{
    /**
     * Get all students in the teacher's advisory class
     */
    public function getAdvisoryStudents()
    {
        try {
            // Log the start of the request
            Log::info('Starting getAdvisoryStudents request');

            $teacherId = Auth::id();
            Log::info('Teacher ID from Auth:', ['teacherId' => $teacherId]);
            
            if (!$teacherId) {
                Log::warning('No teacher ID found in Auth');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Teacher not authenticated'
                ], 401);
            }

            // Log the query we're about to make
            Log::info('Querying for advisory class', [
                'teacherId' => $teacherId,
                'isAdvisory' => true
            ]);

            $advisoryClass = StudentClassModel::where('Adviser_ID', $teacherId)
                ->where('isAdvisory', true)
                ->first();

            Log::info('Advisory class query result:', [
                'found' => !is_null($advisoryClass),
                'class_id' => $advisoryClass ? $advisoryClass->Class_ID : null
            ]);

            if (!$advisoryClass) {
                return response()->json([
                    'status' => 'success',
                    'students' => [],
                    'message' => 'No advisory class found for this teacher'
                ]);
            }

            // Log the student query
            Log::info('Querying for students in advisory class', [
                'class_id' => $advisoryClass->Class_ID
            ]);

            $students = StudentModel::whereHas('studentClasses', function ($query) use ($advisoryClass) {
                $query->where('Class_ID', $advisoryClass->Class_ID)
                      ->where('isAdvisory', true);
            })
            ->select([
                'Student_ID',
                'LRN',
                'FirstName',
                'LastName',
                'MiddleName',
                'Sex',
                'BirthDate',
                'Curriculum',
                'HouseNo',
                'Barangay',
                'Municipality',
                'Province'
            ])
            ->get()
            ->map(function ($student) {
                // Concatenate address fields
                $address = array_filter([
                    $student->HouseNo,
                    $student->Barangay,
                    $student->Municipality,
                    $student->Province
                ]);
                
                $student->Address = implode(', ', $address);
                return $student;
            });

            Log::info('Found students count:', ['count' => $students->count()]);

            return response()->json([
                'status' => 'success',
                'students' => $students
            ]);

        } catch (\Exception $e) {
            Log::error('Advisory students fetch error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch advisory students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects for a specific student in the advisory class
     */
    public function getStudentSubjects($studentId)
    {
        try {
            Log::info('Getting subjects for student:', ['studentId' => $studentId]);
            
            $teacherId = Auth::id();
            
            if (!$teacherId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Teacher not authenticated'
                ], 401);
            }

            // Get all subjects and grades for the student
            $subjects = DB::table('subject_grades')
                ->join('subjects', 'subject_grades.Subject_ID', '=', 'subjects.Subject_ID')
                ->join('students', 'subject_grades.Student_ID', '=', 'students.Student_ID')
                ->join('teachers', 'subject_grades.Teacher_ID', '=', 'teachers.Teacher_ID')
                ->where('subject_grades.Student_ID', $studentId)
                ->select([
                    'subjects.Subject_ID',
                    'subjects.SubjectName',
                    'subjects.SubjectCode',
                    'subject_grades.Q1',
                    'subject_grades.Q2',
                    'subject_grades.Q3',
                    'subject_grades.Q4',
                    'subject_grades.FinalGrade',
                    'subject_grades.Remarks',
                    'teachers.FirstName as TeacherFirstName',
                    'teachers.LastName as TeacherLastName'
                ])
                ->get()
                ->map(function ($subject) {
                    return [
                        'subject_id' => $subject->Subject_ID,
                        'subjectName' => $subject->SubjectName,
                        'subjectCode' => $subject->SubjectCode,
                        'teacher_name' => $subject->TeacherFirstName . ' ' . $subject->TeacherLastName,
                        'grades' => [
                            'Q1' => $subject->Q1,
                            'Q2' => $subject->Q2,
                            'Q3' => $subject->Q3,
                            'Q4' => $subject->Q4,
                            'FinalGrade' => $subject->FinalGrade,
                            'Remarks' => $subject->Remarks
                        ]
                    ];
                });

            return response()->json([
                'status' => 'success',
                'student_subjects' => [
                    [
                        'student_id' => $studentId,
                        'subjects' => $subjects
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Student subjects fetch error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student subjects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get advisory class details
     */
    public function getAdvisoryClassDetails()
    {
        try {
            $teacherId = Auth::id();
            
            $advisoryClass = StudentClassModel::where('Adviser_ID', $teacherId)
                ->where('isAdvisory', true)
                ->with(['class', 'schoolYear'])
                ->first();

            if (!$advisoryClass) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No advisory class found for this teacher'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'advisory_class' => [
                    'class_id' => $advisoryClass->Class_ID,
                    'class_name' => $advisoryClass->class->ClassName,
                    'section' => $advisoryClass->class->Section,
                    'grade_level' => $advisoryClass->class->Grade_Level,
                    'school_year' => $advisoryClass->schoolYear->SchoolYear,
                    'curriculum' => $advisoryClass->class->Curriculum
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch advisory class details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get grades for a specific student
     */
    public function getStudentGrades($studentId)
    {
        try {
            Log::info('Getting grades for student:', ['studentId' => $studentId]);
            
            $teacherId = Auth::id();
            Log::info('Teacher ID from Auth:', ['teacherId' => $teacherId]);
            
            if (!$teacherId) {
                Log::warning('No teacher ID found in Auth');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Teacher not authenticated'
                ], 401);
            }

            // First, verify the student is in the teacher's advisory class
            $advisoryClass = StudentClassModel::where('Adviser_ID', $teacherId)
                ->where('isAdvisory', true)
                ->first();

            if (!$advisoryClass) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No advisory class found for this teacher'
                ], 404);
            }

            // Get all grades for the student
            $grades = SubjectGradeModel::with(['subject', 'teacher'])
                ->where('Student_ID', $studentId)
                ->get()
                ->map(function ($grade) {
                    return [
                        'subject_id' => $grade->Subject_ID,
                        'subject_name' => $grade->subject->SubjectName,
                        'subject_code' => $grade->subject->SubjectCode,
                        'teacher_name' => $grade->teacher->FirstName . ' ' . $grade->teacher->LastName,
                        'quarter_grades' => [
                            'Q1' => $grade->Q1,
                            'Q2' => $grade->Q2,
                            'Q3' => $grade->Q3,
                            'Q4' => $grade->Q4
                        ],
                        'final_grade' => $grade->FinalGrade,
                        'remarks' => $grade->Remarks
                    ];
                });

            Log::info('Found grades count:', ['count' => $grades->count()]);

            return response()->json([
                'status' => 'success',
                'grades' => $grades
            ]);

        } catch (\Exception $e) {
            Log::error('Student grades fetch error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student grades',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
