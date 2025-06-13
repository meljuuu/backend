<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesModel;
use App\Models\StudentClassModel;

class AdminStudentClassController extends Controller
{

    public function indexClass()
    {
        $classes = ClassesModel::where('Status', 'Incomplete')->get();

        return response()->json($classes);
    }

public function indexExcludeIncomplete()
{
    $classes = ClassesModel::with([
            'studentClasses.adviser',
            'studentClasses.student',
            'studentClasses.teacherSubjects.teacher',
            'studentClasses.teacherSubjects.subject'
        ])
        ->withCount('studentClasses')
        ->where('Status', '!=', 'Incomplete')
        ->get()
        ->map(function ($class) {
            // Filter out studentClasses where the student is already in the student_class table
            $class->studentClasses = $class->studentClasses->filter(function ($studentClass) {
                $existingCount = StudentClassModel::where('Student_ID', $studentClass->Student_ID)->count();
                return $existingCount <= 1;
            })->values();

            // Set adviser info
            $firstStudentClass = $class->studentClasses->first();
            $class->adviser_id = $firstStudentClass ? $firstStudentClass->Adviser_ID : null;
            $class->adviser_name = $firstStudentClass && $firstStudentClass->adviser
                ? $firstStudentClass->adviser->name
                : 'Not assigned';

            // Gather unique subject teachers
            $subjectTeachers = collect();
            foreach ($class->studentClasses as $studentClass) {
                foreach ($studentClass->teacherSubjects as $ts) {
                    $subjectTeachers->push([
                        'teacher_id' => $ts->teacher->Teacher_ID ?? null,
                        'teacher_name' => isset($ts->teacher) ? $ts->teacher->FirstName . ' ' . $ts->teacher->LastName : 'Unknown',
                        'subject_id' => $ts->subject->Subject_ID ?? null,
                        'subject_name' => $ts->subject->SubjectName ?? 'Unknown',
                        'subject_code' => $ts->subject_code ?? 'N/A',
                    ]);
                }
            }

            // Remove duplicates by teacher_id + subject_id
            $class->subject_teachers = $subjectTeachers->unique(function ($item) {
                return $item['teacher_id'] . '-' . $item['subject_id'];
            })->values();

            return $class;
        });

    return response()->json($classes);
}


    

    public function indexAllAccepted()
    {
        $classes = ClassesModel::with(['studentClasses.adviser', 'studentClasses.student'])
        ->withCount('studentClasses')
        ->where('Status', 'Accepted')
        ->get()
        ->map(function ($class) {
            $firstStudentClass = $class->studentClasses->first();
            $class->adviser_id = $firstStudentClass ? $firstStudentClass->Adviser_ID : null;
            $class->adviser_name = $firstStudentClass && $firstStudentClass->adviser
                ? $firstStudentClass->adviser->name 
                : 'Not assigned';
            return $class;
        });
    
        return response()->json($classes);
    }

    public function indexAllAClasses()
    {
        $classes = ClassesModel::with(['studentClasses.adviser', 'studentClasses.student'])
        ->withCount('studentClasses')
        ->get()
        ->map(function ($class) {
            $firstStudentClass = $class->studentClasses->first();
            $class->adviser_id = $firstStudentClass ? $firstStudentClass->Adviser_ID : null;
            $class->adviser_name = $firstStudentClass && $firstStudentClass->adviser
                ? $firstStudentClass->adviser->name 
                : 'Not assigned';
            return $class;
        });
    
        return response()->json($classes);
    }
    
    

    public function assignStudentsToClass(Request $request)
    {
        try {
            $validated = $request->validate([
                'class_id' => 'required|exists:classes,Class_ID',
                'sy_id' => 'required|exists:school_years,SY_ID',
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'exists:students,Student_ID',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        }

        $classId = $validated['class_id'];
        $syId = $validated['sy_id'];
        $studentIds = $validated['student_ids'];

        // Fetch class info
        $class = ClassesModel::findOrFail($classId);
        $teacherId = $class->Teacher_ID;
        $className = $class->ClassName;

        $created = [];

        foreach ($studentIds as $studentId) {
            $alreadyAssigned = StudentClassModel::where('Student_ID', $studentId)
                ->where('Class_ID', $classId)
                ->where('SY_ID', $syId)
                ->first();

            if (!$alreadyAssigned) {
                $studentClass = StudentClassModel::create([
                    'Student_ID' => $studentId,
                    'Class_ID' => $classId,
                    'SY_ID' => $syId,
                    'Teacher_ID' => $teacherId,
                    'ClassName' => $className,
                    'isAdvisory' => false,
                ]);

                $created[] = $studentClass;
            }
        }

        return response()->json([
            'message' => count($created) . ' student(s) successfully assigned to the class.',
            'assigned' => $created,
        ], 201);
    }
}
