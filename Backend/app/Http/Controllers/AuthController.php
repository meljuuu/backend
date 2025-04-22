<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\TeacherModel;

class AuthController extends Controller
{
    public function login(Request $request)

    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $teacher = TeacherModel::where('Email', $credentials['email'])->first();

        if (!$teacher || !Hash::check($credentials['password'], $teacher->Password)) {
            return response()->json(['error' => 'Invalid email or password.'], 401);
        }

        $token = $teacher->createToken('TeacherToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'teacher' => $teacher,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)

    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
