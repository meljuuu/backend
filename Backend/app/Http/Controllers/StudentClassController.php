<?php

namespace App\Http\Controllers;

use App\Models\StudentClassModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StudentClassController extends Controller
{
   
    public function index()
    {
        $list = StudentClassModel::with(['student' => function($query) {
            $query->select('Student_ID', 'firstName', 'middleName', 'lastName', 'lrn', 'sex', 'birthDate', 'curriculum');
        }, 'class', 'schoolYear', 'teacher', 'adviser', 'teacherSubjects'])
            ->get();

        return response()->json($list);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,Student_ID',
            'class_id' => 'required|exists:classes,Class_ID',
            'class_name' => 'nullable|string',
            'sy_id' => 'required|exists:school_years,SY_ID',
            'adviser_id' => 'nullable|exists:teachers,Teacher_ID',
            'teacher_subject_ids' => 'required|array',
            'teacher_subject_ids.*' => 'integer|exists:teachers_subject,id',
            'is_advisory' => 'boolean',
        ]);
    
        // If this is an advisory class, ensure only one teacher is marked as advisory
        if ($request->is_advisory) {
            // Remove advisory status from other teachers in this class
            StudentClassModel::where('Class_ID', $request->class_id)
                ->where('isAdvisory', true)
                ->update(['isAdvisory' => false]);
        }
    
        $studentClassRecords = [];
    
        foreach ($request->student_ids as $studentId) {
            $studentClass = StudentClassModel::create([
                'Student_ID' => $studentId,
                'Class_ID' => $request->class_id,
                'ClassName' => $request->class_name,
                'SY_ID' => $request->sy_id,
                'Adviser_ID' => $request->adviser_id,
                'isAdvisory' => $request->is_advisory ?? false,
            ]);
    
            // Attach teacher_subjects via pivot
            $studentClass->teacherSubjects()->attach($request->teacher_subject_ids);
    
            $studentClassRecords[] = $studentClass;
        }
    
        // ✅ Update class status from 'incomplete' to 'pending'
        DB::table('classes')
            ->where('Class_ID', $request->class_id)
            ->where('Status', 'incomplete')
            ->update(['Status' => 'pending']);
    
        return response()->json([
            'message' => 'Student classes and subject assignments created successfully.',
            'data' => $studentClassRecords
        ]);
    }

    public function addStudentsToClass(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,Student_ID',
            'class_id' => 'required|exists:classes,Class_ID',
        ]);
    
        // Fetch existing class setup from any existing student_class row in that class
        $existingStudentClass = StudentClassModel::where('Class_ID', $request->class_id)->first();
    
        if (!$existingStudentClass) {
            return response()->json([
                'message' => 'No existing class data found to copy from.'
            ], 404);
        }
    
        // Update class status to 'pending'
        $class = \App\Models\ClassesModel::find($request->class_id);
        if ($class) {
            $class->status = 'pending';
            $class->save();
        }
    
        $teacherSubjectIds = $existingStudentClass->teacherSubjects()->pluck('teachers_subject.id')->toArray();
        $newStudentClasses = [];
    
        foreach ($request->student_ids as $studentId) {
            // Skip if student is already in the class
            $alreadyExists = StudentClassModel::where('Class_ID', $request->class_id)
                ->where('Student_ID', $studentId)
                ->exists();
    
            if ($alreadyExists) {
                continue; // Skip existing student
            }
    
            $newStudentClass = StudentClassModel::create([
                'Student_ID' => $studentId,
                'Class_ID' => $existingStudentClass->Class_ID,
                'ClassName' => $existingStudentClass->ClassName,
                'SY_ID' => $existingStudentClass->SY_ID,
                'Adviser_ID' => $existingStudentClass->Adviser_ID,
                'isAdvisory' => $existingStudentClass->isAdvisory,
            ]);
    
            $newStudentClass->teacherSubjects()->attach($teacherSubjectIds);
            $newStudentClasses[] = $newStudentClass;
        }
    
        return response()->json([
            'message' => count($newStudentClasses) > 0 
                ? 'Students successfully added to class and class status set to pending.'
                : 'No new students were added (possibly already enrolled).',
            'data' => $newStudentClasses
        ]);
    }

    public function removeStudentsFromClass(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,Student_ID',
            'class_id' => 'required|exists:classes,Class_ID',
        ]);

        $removedStudents = [];

        foreach ($request->student_ids as $studentId) {
            $studentClass = StudentClassModel::where('Class_ID', $request->class_id)
                ->where('Student_ID', $studentId)
                ->first();

            if ($studentClass) {
                // Detach related teacher subjects if any
                $studentClass->teacherSubjects()->detach();

                // Delete the student from the class
                $studentClass->delete();

                $removedStudents[] = $studentId;
            }
        }

        // If at least one student was removed, update class status to 'pending'
        if (count($removedStudents) > 0) {
            $class = \App\Models\ClassesModel::find($request->class_id);
            if ($class) {
                $class->status = 'pending';
                $class->save();
            }
        }

        return response()->json([
            'message' => count($removedStudents) > 0 
                ? 'Students successfully removed from class and class status set to pending.'
                : 'No students were removed (possibly not enrolled).',
            'removed_student_ids' => $removedStudents
        ]);
    }
    
    
    
    public function show($id)
    {
        $studentClass = StudentClassModel::with(['teacherSubjects.subject'])->findOrFail($id);

        $subjects = $studentClass->teacherSubjects->map(function($ts) {
            return $ts->subject;
        })->unique('Subject_ID')->values();

        return response()->json([
            'status' => 'success',
            'subjects' => $subjects
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Student_ID'        => ['sometimes', 'exists:students,Student_ID'],
            'Class_ID'          => ['sometimes', 'exists:classes,Class_ID'],
            'ClassName'         => ['sometimes', 'string', 'max:255'],
            'SY_ID'             => ['sometimes', 'exists:school_years,SY_ID'],
            'Teacher_ID'        => ['nullable', 'exists:teachers,Teacher_ID'],
            'Adviser_ID'        => ['nullable', 'exists:teachers,Teacher_ID'],
            'TeacherSubject_ID' => ['nullable', 'exists:teachers_subject,id'],
            'isAdvisory'        => ['boolean'],
            'Status'            => ['sometimes', Rule::in(['Pending', 'Accepted', 'Declined', 'Incomplete'])],
        ]);

        $item = StudentClass::findOrFail($id);
        $item->update($validated);

        return response()->json([
            'message' => 'StudentClass updated',
            'data'    => $item
        ]);
    }

    public function destroy($id)
    {
        $item = StudentClass::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'StudentClass deleted']);
    }

    public function checkAdvisoryStatus($classId, $teacherId)
    {
        $isAdvisory = StudentClassModel::where('Class_ID', $classId)
            ->where('Adviser_ID', $teacherId)
            ->where('isAdvisory', true)
            ->exists();

        return response()->json([
            'status' => 'success',
            'isAdvisory' => $isAdvisory
        ]);
    }

    public function getStudentsByClass($classId)
    {
        try {
            $students = StudentClassModel::with(['student' => function($query) {
                $query->select('Student_ID', 'FirstName', 'MiddleName', 'LastName', 'LRN', 'Sex', 'BirthDate', 'ContactNumber', 'Barangay', 'Municipality', 'Province');
            }])
            ->where('Class_ID', $classId)
            ->get()
            ->map(function($studentClass) {
                $student = $studentClass->student;
                return [
                    'Student_ID' => $student->Student_ID,
                    'FirstName' => $student->FirstName,
                    'MiddleName' => $student->MiddleName,
                    'LastName' => $student->LastName,
                    'LRN' => $student->LRN,
                    'Sex' => $student->Sex,
                    'BirthDate' => $student->BirthDate,
                    'ContactNumber' => $student->ContactNumber,
                    'Barangay' => $student->Barangay,
                    'Municipality' => $student->Municipality,
                    'Province' => $student->Province,
                    'isAdvisory' => $studentClass->isAdvisory
                ];
            });

            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students: ' . $e->getMessage()
            ], 500);
        }
    }
}
