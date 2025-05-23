<?php

namespace App\Http\Controllers;

use App\Models\TeachersSubject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Research;

class TeacherSubjectController extends Controller
{
    public function getAllSubject()
    {
        try {
            $teachersSubjects = TeachersSubject::with(['teacher', 'subject'])->get();
    
            return response()->json([
                'teachersSubjects' => $teachersSubjects
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve teacher-subject records.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}