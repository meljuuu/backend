<?php

namespace App\Http\Controllers;

use App\Models\ClassesModel;
use App\Models\ClassSubjectModel;
use App\Models\StudentClassModel;
use App\Models\SubjectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function getClasses()
    {
        try {
            $classes = ClassesModel::with(['subjects', 'students'])
                ->get()
                ->map(function ($class) {
                    return [
                        'class_id' => $class->Class_ID,
                        'trackStand' => $class->Track,
                        'classType' => $class->students->first()?->pivot->isAdvisory ? 'Advisory' : 'Subject',
                        'className' => $class->ClassName,
                        'gradeLevel' => $class->Grade_Level,
                        'subject_id' => $class->subjects->first()?->Subject_ID,
                        'subjectName' => $class->subjects->first()?->SubjectName ?? 'No Subject',
                        'totalStudents' => $class->students->count()
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $classes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getClassDetails($classId)
    {
        try {
            $class = ClassesModel::with(['subjects', 'students'])
                ->findOrFail($classId);

            $maleCount = $class->students->whereIn('Sex', ['M', 'Male'])->count();
            $femaleCount = $class->students->whereIn('Sex', ['F', 'Female'])->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'class_id' => $class->Class_ID,
                    'trackStand' => $class->Track,
                    'classType' => $class->students->first()?->pivot->isAdvisory ? 'Advisory' : 'Subject',
                    'className' => $class->ClassName,
                    'gradeLevel' => $class->Grade_Level,
                    'subject_id' => $class->subjects->first()?->Subject_ID,
                    'subjectName' => $class->subjects->first()?->SubjectName ?? 'No Subject',
                    'maleCount' => $maleCount,
                    'femaleCount' => $femaleCount,
                    'totalStudents' => $class->students->count(),
                    'students' => $class->students->map(function ($student) {
                        return [
                            'student_id' => $student->Student_ID,
                            'lrn' => $student->LRN,
                            'firstName' => $student->FirstName,
                            'lastName' => $student->LastName,
                            'middleName' => $student->MiddleName,
                            'sex' => $student->Sex,
                            'birthDate' => $student->BirthDate,
                            'contactNumber' => $student->ContactNumber,
                            'address' => $student->Address
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getClassStudents($classId)
    {
        try {
            \Log::info('Attempting to find class with ID: ' . $classId);
            
            // First try to find the class directly
            $class = ClassesModel::with(['students', 'studentClasses.teacherSubjects.subject'])
                ->find($classId);
            
            if ($class) {
                \Log::info('Found class directly: ' . $class->ClassName);
            } else {
                \Log::info('Class not found directly, trying through subject relationship');
                
                // Try to find it through the subject relationship using the correct pivot tables
                $class = ClassesModel::whereHas('studentClasses.teacherSubjects', function($query) use ($classId) {
                    $query->where('teachers_subject.subject_id', $classId);
                })
                ->with(['students', 'studentClasses.teacherSubjects.subject'])
                ->first();
                
                if ($class) {
                    \Log::info('Found class through subject relationship: ' . $class->ClassName);
                } else {
                    \Log::info('Class not found through subject relationship either');
                }
            }
            
            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }
            
            // Get students through the student_class relationship
            $students = $class->students()->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $students->map(function ($student) {
                    return [
                        'student_id' => $student->Student_ID,
                        'lrn' => $student->LRN,
                        'firstName' => $student->FirstName,
                        'lastName' => $student->LastName,
                        'middleName' => $student->MiddleName,
                        'sex' => $student->Sex,
                        'birthDate' => $student->BirthDate,
                        'contactNumber' => $student->ContactNumber,
                        'houseNo' => $student->HouseNo,
                        'barangay' => $student->Barangay,
                        'municipality' => $student->Municipality,
                        'province' => $student->Province
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getClassStudents: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 