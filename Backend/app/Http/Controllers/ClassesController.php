<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesModel;
use App\Models\StudentClassTeacherSubject;
use App\Models\GradesModel;
use Illuminate\Support\Facades\DB;

class ClassesController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('Classes index method called');
        \Log::info('Request headers:', $request->headers->all());
        \Log::info('Request query parameters:', $request->query());
        
        $teacherId = $request->query('teacher_id');
        \Log::info('Received teacher ID: ' . $teacherId);
        
        if (!$teacherId) {
            \Log::error('No teacher ID provided');
            return response()->json([
                'status' => 'error',
                'message' => 'Teacher ID is required'
            ], 400);
        }
        
        try {
            // First, let's verify the teacher exists
            $teacherExists = DB::table('teachers')
                ->where('Teacher_ID', $teacherId)
                ->exists();
            
            \Log::info('Teacher exists: ' . ($teacherExists ? 'yes' : 'no'));
            
            $query = ClassesModel::with([
                'schoolYear',
                'adviser',
                'studentClasses' => function($query) use ($teacherId) {
                    $query->where(function($q) use ($teacherId) {
                        $q->where('Adviser_ID', $teacherId)
                          ->where('isAdvisory', true)
                          ->orWhereHas('teacherSubjects', function($q) use ($teacherId) {
                              $q->where('teacher_id', $teacherId);
                          });
                    });
                },
                'studentClasses.teacherSubjects' => function($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                },
                'studentClasses.teacherSubjects.subject'
            ])
            ->where('Status', 'accepted')
            ->where(function($query) use ($teacherId) {
                $query->where('Adviser_ID', $teacherId)  // Check if teacher is the adviser
                      ->orWhereHas('studentClasses.teacherSubjects', function($q) use ($teacherId) {
                          $q->where('teacher_id', $teacherId);  // Check if teacher teaches any subject
                      });
            });
            
            \Log::info('SQL Query: ' . $query->toSql());
            \Log::info('Query Bindings: ' . json_encode($query->getBindings()));
            
            $classes = $query->get();
            \Log::info('Number of classes found: ' . $classes->count());
            
            $mappedClasses = $classes->map(function ($class) use ($teacherId) {
                $subject = null;
                $isAdvisory = false;
                
                // Check if this teacher is the adviser for this class
                $isAdviser = $class->Adviser_ID == $teacherId;
                
                foreach ($class->studentClasses as $studentClass) {
                    // Log each student class for debugging
                    \Log::info('Checking student class:', [
                        'Adviser_ID' => $studentClass->Adviser_ID,
                        'isAdvisory' => $studentClass->isAdvisory,
                        'teacher_id' => $studentClass->teacherSubjects->pluck('teacher_id')
                    ]);
                    
                    if ($studentClass->Adviser_ID == $teacherId && $studentClass->isAdvisory) {
                        $isAdvisory = true;
                    }
                    
                    foreach ($studentClass->teacherSubjects as $teacherSubject) {
                        if ($teacherSubject->teacher_id == $teacherId) {
                            $subject = $teacherSubject->subject;
                            break 2;
                        }
                    }
                }

                return [
                    'class_id' => (string)$class->Class_ID,
                    'trackStand' => $class->Track,
                    'classType' => $isAdvisory ? 'Advisory' : $class->Curriculum,
                    'className' => $class->ClassName,
                    'subjectName' => $subject ? $subject->SubjectName : 'No Subject Assigned',
                    'subject_id' => $subject ? (string)$subject->Subject_ID : null,
                    'gradeLevel' => $class->Grade_Level,
                    'isAdvisory' => $isAdvisory,
                    'isAdviser' => $isAdviser  // Add this field to track if teacher is the adviser
                ];
            });

            \Log::info('Mapped classes:', $mappedClasses->toArray());
            
            return response()->json([
                'status' => 'success',
                'data' => $mappedClasses
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in ClassesController@index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching classes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showSubjectsForClass($classId)
    {
        $class = \App\Models\ClassesModel::with([
            'studentClasses.teacherSubjects.subject'
        ])->findOrFail($classId);

        // Collect unique subjects from all studentClasses
        $subjects = collect();
        foreach ($class->studentClasses as $studentClass) {
            foreach ($studentClass->teacherSubjects as $teacherSubject) {
                if ($teacherSubject->subject) {
                    $subjects->push($teacherSubject->subject);
                }
            }
        }
        $uniqueSubjects = $subjects->unique('Subject_ID')->values();

        return response()->json([
            'status' => 'success',
            'subjects' => $uniqueSubjects
        ]);
    }

    public function getStudentsForSubject($subjectId)
    {
        try {
            // First get the class ID for this subject
            $classId = DB::table('student_class_teacher_subject as scts')
                ->join('teachers_subject as ts', 'scts.teacher_subject_id', '=', 'ts.id')
                ->where('ts.subject_id', $subjectId)
                ->value('scts.student_class_id');

            if (!$classId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No class found for this subject'
                ], 404);
            }

            $students = DB::table('student_class as sc')
                ->join('students as s', 'sc.Student_ID', '=', 's.Student_ID')
                ->leftJoin('student_class_teacher_subject as scts', function($join) use ($subjectId) {
                    $join->on('sc.StudentClass_ID', '=', 'scts.student_class_id')
                        ->where('scts.teacher_subject_id', '=', function($query) use ($subjectId) {
                            $query->select('id')
                                ->from('teachers_subject')
                                ->where('subject_id', $subjectId)
                                ->limit(1);
                        });
                })
                ->leftJoin('teachers_subject as ts', 'scts.teacher_subject_id', '=', 'ts.id')
                ->leftJoin('subject_grades as sg', function($join) use ($subjectId) {
                    $join->on('s.Student_ID', '=', 'sg.Student_ID')
                        ->where('sg.Subject_ID', '=', $subjectId);
                })
                ->where('sc.Class_ID', $classId)
                ->select(
                    's.Student_ID as student_id',
                    's.LRN as lrn',
                    's.FirstName as firstName',
                    's.MiddleName as middleName',
                    's.LastName as lastName',
                    's.Sex as sex',
                    's.BirthDate as birthDate',
                    's.ContactNumber as contactNumber',
                    's.HouseNo',
                    's.Barangay',
                    's.Municipality',
                    's.Province',
                    'sc.Class_ID as class_id',
                    'sc.ClassName as class_name',
                    'sg.Q1 as first_quarter',
                    'sg.Q2 as second_quarter',
                    'sg.Q3 as third_quarter',
                    'sg.Q4 as fourth_quarter'
                )
                ->get()
                ->map(function($student) {
                    return [
                        'student_id' => $student->student_id,
                        'lrn' => $student->lrn,
                        'firstName' => $student->firstName,
                        'middleName' => $student->middleName ?? '',
                        'lastName' => $student->lastName,
                        'sex' => $student->sex,
                        'birthDate' => $student->birthDate,
                        'contactNumber' => $student->contactNumber,
                        'address' => $student->HouseNo . ', ' . 
                                    $student->Barangay . ', ' . 
                                    $student->Municipality . ', ' . 
                                    $student->Province,
                        'class_id' => $student->class_id,
                        'class_name' => $student->class_name,
                        'grades' => [
                            'first' => $student->first_quarter,
                            'second' => $student->second_quarter,
                            'third' => $student->third_quarter,
                            'fourth' => $student->fourth_quarter
                        ]
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStudentsForClass($classId)
    {
        try {
            $students = DB::table('student_class as sc')
                ->join('students as s', 'sc.Student_ID', '=', 's.Student_ID')
                ->where('sc.Class_ID', $classId)
                ->select(
                    's.Student_ID as student_id',
                    's.LRN as lrn',
                    's.FirstName as firstName',
                    's.MiddleName as middleName',
                    's.LastName as lastName',
                    's.Sex as sex',
                    's.BirthDate as birthDate',
                    's.ContactNumber as contactNumber',
                    's.HouseNo',
                    's.Barangay',
                    's.Municipality',
                    's.Province'
                )
                ->get()
                ->map(function($student) {
                    return [
                        'student_id' => $student->student_id,
                        'lrn' => $student->lrn,
                        'firstName' => $student->firstName,
                        'middleName' => $student->middleName ?? '',
                        'lastName' => $student->lastName,
                        'sex' => $student->sex,
                        'birthDate' => $student->birthDate,
                        'contactNumber' => $student->contactNumber,
                        'address' => $student->HouseNo . ', ' . 
                                    $student->Barangay . ', ' . 
                                    $student->Municipality . ', ' . 
                                    $student->Province
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students: ' . $e->getMessage()
            ], 500);
        }
    }
}