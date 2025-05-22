<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesModel;

class ClassesController extends Controller
{
    public function index()
    {
        // Fetch classes with relationships and filter by status = 'accepted'
        $classes = ClassesModel::with(['schoolYear', 'adviser', 'subject'])
            ->where('status', 'accepted')
            ->get()
            ->map(function ($class) {
                return [
                    'class_id' => $class->Class_ID,
                    'trackStand' => $class->Track, // Maps to 'Track' in the database
                    'classType' => $class->Curriculum, // Maps to 'Curriculum' in the database
                    'className' => $class->ClassName,
                    'subjectName' => $class->subject ? $class->subject->SubjectName : null,
                    'subject_id' => $class->subject ? $class->subject->Subject_ID : null,
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