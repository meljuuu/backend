<?php

namespace App\Http\Controllers;

use App\Models\TeachersSubject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Research;
use App\Models\StudentClassModel;

class TeacherSubjectController extends Controller
{
    public function getAllSubject()
    {
        $teachersSubjects = TeachersSubject::with(['subject', 'teacher'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $teachersSubjects->map(function($ts) {
                // Get advisory status for this teacher-subject combination
                $isAdvisory = StudentClassModel::where('Adviser_ID', $ts->teacher->Teacher_ID)
                    ->where('isAdvisory', true)
                    ->exists();

                return [
                    'id' => $ts->id,
                    'subject' => [
                        'id' => $ts->subject->Subject_ID,
                        'name' => $ts->subject->SubjectName,
                        'code' => $ts->subject->SubjectCode
                    ],
                    'teacher' => [
                        'id' => $ts->teacher->Teacher_ID,
                        'name' => $ts->teacher->FirstName . ' ' . $ts->teacher->LastName,
                        'isAdvisory' => $isAdvisory
                    ]
                ];
            })
        ]);
    }
}