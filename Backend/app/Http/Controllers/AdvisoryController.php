<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;
use App\Models\StudentClassModel;
use App\Models\TeachersSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\StudentClassTeacherSubject;

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
            Log::info('Teacher ID from Auth:', ['teacherId' => $teacherId]);
            
            if (!$teacherId) {
                Log::warning('No teacher ID found in Auth');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Teacher not authenticated'
                ], 401);
            }

            // First, get the student's class
            $studentClass = StudentClassModel::where('Student_ID', $studentId)
                ->where('isAdvisory', true)
                ->first();

            Log::info('Student class query result:', [
                'found' => !is_null($studentClass),
                'student_id' => $studentId
            ]);

            if (!$studentClass) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found in any advisory class'
                ], 404);
            }

            // Get the subjects through the student_class_teacher_subject pivot table
            $subjects = StudentClassTeacherSubject::with(['teacherSubject.subject', 'teacherSubject.teacher'])
                ->where('student_class_id', $studentClass->StudentClass_ID)
                ->get()
                ->map(function ($item) {
                    return [
                        'subject_id' => $item->teacherSubject->subject->Subject_ID,
                        'subjectName' => $item->teacherSubject->subject->SubjectName,
                        'subjectCode' => $item->teacherSubject->subject->SubjectCode,
                        'teacher_id' => $item->teacherSubject->teacher->Teacher_ID,
                        'teacher_name' => $item->teacherSubject->teacher->FirstName . ' ' . 
                                        $item->teacherSubject->teacher->LastName,
                        'class_id' => $item->studentClass->Class_ID
                    ];
                })
                ->unique('subject_id')
                ->values();

            Log::info('Found subjects count:', ['count' => $subjects->count()]);

            return response()->json([
                'status' => 'success',
                'subjects' => $subjects
            ]);

        } catch (\Exception $e) {
            Log::error('Student subjects fetch error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
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
}
