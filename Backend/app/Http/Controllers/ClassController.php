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
            \Log::info('Attempting to fetch class details for ID: ' . $classId);
            
            // First check if the class exists and load the students relationship
            $class = ClassesModel::with('students')->find($classId);
            
            if (!$class) {
                \Log::warning('Class not found with ID: ' . $classId);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            // Get total students count
            $totalStudents = $class->students->count();

            // Count male and female students
            $maleCount = $class->students->filter(function($student) {
                return in_array(strtolower($student->Sex), ['m', 'male']);
            })->count();

            $femaleCount = $class->students->filter(function($student) {
                return in_array(strtolower($student->Sex), ['f', 'female']);
            })->count();

            \Log::info('Student counts:', [
                'total' => $totalStudents,
                'male' => $maleCount,
                'female' => $femaleCount
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'className' => $class->ClassName,
                    'section' => $class->Section,
                    'gradeLevel' => $class->Grade_Level,
                    'track' => $class->Track,
                    'curriculum' => $class->Curriculum,
                    'maleCount' => $maleCount,
                    'femaleCount' => $femaleCount,
                    'totalStudents' => $totalStudents
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getClassDetails: ' . $e->getMessage(), [
                'class_id' => $classId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching class details: ' . $e->getMessage()
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
                    // Handle missing address fields
                    $houseNo = $student->HouseNo ?? '';
                    $barangay = $student->Barangay ?? '';
                    $municipality = $student->Municipality ?? '';
                    $province = $student->Province ?? '';

                    // Build address only if at least one field exists
                    $addressParts = array_filter([$houseNo, $barangay, $municipality, $province]);
                    $address = $addressParts ? implode(', ', $addressParts) : 'No address provided';

                    return [
                        'student_id' => $student->student_id,
                        'lrn' => $student->lrn,
                        'firstName' => $student->firstName,
                        'middleName' => $student->middleName ?? '',
                        'lastName' => $student->lastName,
                        'sex' => $student->sex,
                        'birthDate' => $student->birthDate,
                        'contactNumber' => $student->contactNumber,
                        'address' => $address // Use the cleaned-up address
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