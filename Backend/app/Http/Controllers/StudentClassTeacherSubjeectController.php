<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentClassTeacherSubject;
use App\Models\StudentClassModel;
use App\Models\TeachersSubject;
use Illuminate\Support\Facades\DB;

class StudentClassTeacherSubjectController extends Controller
{
    /**
     * Get all student class teacher subject relationships
     */
    public function index()
    {
        $relationships = StudentClassTeacherSubject::with([
            'studentClass',
            'teacherSubject.subject',
            'teacherSubject.teacher'
        ])->get();

        return response()->json([
            'status' => 'success',
            'data' => $relationships
        ]);
    }

    /**
     * Get subjects for a specific class
     */
    public function getSubjectsForClass($classId)
    {
        $subjects = StudentClassTeacherSubject::with(['teacherSubject.subject'])
            ->whereHas('studentClass', function($query) use ($classId) {
                $query->where('Class_ID', $classId);
            })
            ->get()
            ->pluck('teacherSubject.subject')
            ->unique('Subject_ID')
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $subjects
        ]);
    }

    /**
     * Get teachers for a specific class
     */
    public function getTeachersForClass($classId)
    {
        $teachers = StudentClassTeacherSubject::with(['teacherSubject.teacher'])
            ->whereHas('studentClass', function($query) use ($classId) {
                $query->where('Class_ID', $classId);
            })
            ->get()
            ->pluck('teacherSubject.teacher')
            ->unique('Teacher_ID')
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $teachers
        ]);
    }

    /**
     * Assign subjects to a class
     */
    public function assignSubjectsToClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,Class_ID',
            'teacher_subject_ids' => 'required|array',
            'teacher_subject_ids.*' => 'exists:teachers_subject,id',
            'adviser_id' => 'nullable|exists:teachers,Teacher_ID',
            'is_advisory' => 'boolean'
        ]);

        $studentClass = StudentClassModel::where('Class_ID', $request->class_id)->first();

        if (!$studentClass) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        }

        // If this is an advisory assignment
        if ($request->is_advisory && $request->adviser_id) {
            // Remove advisory status from other teachers in this class
            StudentClassModel::where('Class_ID', $request->class_id)
                ->where('isAdvisory', true)
                ->update(['isAdvisory' => false]);

            // Set the new adviser
            $studentClass->update([
                'Adviser_ID' => $request->adviser_id,
                'isAdvisory' => true
            ]);
        }

        // Attach teacher subjects to the student class
        $studentClass->teacherSubjects()->attach($request->teacher_subject_ids);

        return response()->json([
            'status' => 'success',
            'message' => 'Subjects and advisory status assigned to class successfully'
        ]);
    }

    /**
     * Remove subjects from a class
     */
    public function removeSubjectsFromClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,Class_ID',
            'teacher_subject_ids' => 'required|array',
            'teacher_subject_ids.*' => 'exists:teachers_subject,id'
        ]);

        $studentClass = StudentClassModel::where('Class_ID', $request->class_id)->first();

        if (!$studentClass) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        }

        // Detach teacher subjects from the student class
        $studentClass->teacherSubjects()->detach($request->teacher_subject_ids);

        return response()->json([
            'status' => 'success',
            'message' => 'Subjects removed from class successfully'
        ]);
    }

    /**
     * Get all classes for a specific teacher
     */
    public function getClassesForTeacher($teacherId)
    {
        $classes = StudentClassTeacherSubject::with(['studentClass.class'])
            ->whereHas('teacherSubject', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->get()
            ->pluck('studentClass.class')
            ->unique('Class_ID')
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $classes
        ]);
    }

    /**
     * Get all subjects for a specific teacher in a class
     */
    public function getTeacherSubjectsInClass($teacherId, $classId)
    {
        $subjects = StudentClassTeacherSubject::with(['teacherSubject.subject'])
            ->whereHas('studentClass', function($query) use ($classId) {
                $query->where('Class_ID', $classId);
            })
            ->whereHas('teacherSubject', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->get()
            ->pluck('teacherSubject.subject')
            ->unique('Subject_ID')
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $subjects
        ]);
    }

    /**
     * Get all subjects for a class with their teachers
     */
    public function getClassSubjectsWithTeachers($classId)
    {
        $subjects = StudentClassTeacherSubject::with([
            'teacherSubject.subject',
            'teacherSubject.teacher'
        ])
        ->whereHas('studentClass', function($query) use ($classId) {
            $query->where('Class_ID', $classId);
        })
        ->get()
        ->map(function($item) {
            return [
                'subject' => [
                    'id' => $item->teacherSubject->subject->Subject_ID,
                    'name' => $item->teacherSubject->subject->SubjectName,
                    'code' => $item->teacherSubject->subject->SubjectCode
                ],
                'teacher' => [
                    'id' => $item->teacherSubject->teacher->Teacher_ID,
                    'name' => $item->teacherSubject->teacher->FirstName . ' ' . 
                             $item->teacherSubject->teacher->LastName
                ]
            ];
        })
        ->unique('subject.id')
        ->values();

        return response()->json([
            'status' => 'success',
            'data' => $subjects
        ]);
    }
}