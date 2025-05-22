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
        $teachersSubjects = TeachersSubject::with('subject')->get();

        return response()->json([
            'status' => 'success',
            'teachersSubjects' => $teachersSubjects
        ]);
    }
}