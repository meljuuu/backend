<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesModel;

class ClassesController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->query('teacher_id');
        
        \Log::info('Filtering classes for teacher ID: ' . $teacherId);
        
        $query = ClassesModel::with([
            'schoolYear',
            'adviser',
            'studentClasses.teacherSubjects' => function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            },
            'studentClasses.teacherSubjects.subject'
        ])
        ->where('status', 'accepted')
        ->where('Adviser_ID', $teacherId);
        
        $classes = $query->get()
            ->map(function ($class) use ($teacherId) {
                // Get the subject for this teacher in this class
                $subject = null;
                foreach ($class->studentClasses as $studentClass) {
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
                    'classType' => $class->Curriculum,
                    'className' => $class->ClassName,
                    'subjectName' => $subject ? $subject->SubjectName : 'No Subject Assigned', // Show actual subject name
                    'subject_id' => $subject ? $subject->Subject_ID : null,
                    'gradeLevel' => $class->Grade_Level,
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
}