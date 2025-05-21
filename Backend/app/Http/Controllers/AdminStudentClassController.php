<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesModel;
use App\Models\StudentClassModel;

class AdminStudentClassController extends Controller
{

    public function indexClass()
    {
        // Get all classes
        $classes = ClassesModel::where('Status', 'Incomplete')->get();

        // Return as JSON (for API) or pass to a view
        return response()->json($classes);
    }

    public function indexExcludeIncomplete()
    {
        $classes = ClassesModel::with(['studentClasses.adviser']) // Load adviser via studentClasses
            ->withCount('studentClasses')
            ->where('Status', '!=', 'Incomplete')
            ->get()
            ->map(function ($class) {
                // Get the first StudentClass and retrieve adviser name if available
                $firstStudentClass = $class->studentClasses->first();
                $class->adviser_id = $firstStudentClass ? $firstStudentClass->Adviser_ID : null;
                $class->adviser_name = $firstStudentClass && $firstStudentClass->adviser
                    ? $firstStudentClass->adviser->name // Adjust this to your teacher's name column
                    : 'Not assigned';
                return $class;
            });
    
        return response()->json($classes);
    }
    
    

    /**
     * Assign multiple students to a class.
     */
    public function assignStudentsToClass(Request $request)
    {
        try {
            // Validate request input
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
                    'isAdvisory' => false, // default to false; can be changed later
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
