<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesModel;

class ClassesController extends Controller
{
    public function index()
    {
        // Fetch classes with relationships and filter by status = 'accepted'
        $classes = ClassesModel::with([
            'schoolYear',
            'adviser',
            'studentClasses.teacherSubjects.subject'
        ])
        ->where('status', 'accepted')
        ->get()
        ->map(function ($class) {
            // Get the first subject from the relationship chain
            $subject = null;
            if ($class->studentClasses->isNotEmpty()) {
                $firstStudentClass = $class->studentClasses->first();
                if ($firstStudentClass->teacherSubjects->isNotEmpty()) {
                    $firstTeacherSubject = $firstStudentClass->teacherSubjects->first();
                    $subject = $firstTeacherSubject->subject;
                }
            }

            return [
                'class_id' => $class->Class_ID,
                'trackStand' => $class->Track,
                'classType' => $class->Curriculum,
                'className' => $class->ClassName,
                'subjectName' => $subject ? $subject->SubjectName : 'No Subject Assigned',
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