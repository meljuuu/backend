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
        $teacherId = $request->query('teacher_id');
        
        \Log::info('Filtering classes for teacher ID: ' . $teacherId);
        
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
        ->where('status', 'accepted');
        
        $classes = $query->get()
            ->map(function ($class) use ($teacherId) {
                $subject = null;
                $isAdvisory = false;
                
                foreach ($class->studentClasses as $studentClass) {
                    // Check if this is an advisory class
                    if ($studentClass->Adviser_ID == $teacherId && $studentClass->isAdvisory) {
                        $isAdvisory = true;
                    }
                    
                    // Get subject information regardless of advisory status
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
                    'subject_id' => $subject ? $subject->Subject_ID : null,
                    'gradeLevel' => $class->Grade_Level,
                    'isAdvisory' => $isAdvisory
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $classes
        ]);
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
}