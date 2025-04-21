<?php

namespace App\Http\Controllers;

use App\Models\TeacherModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
     // Function to create a new teacher
     public function createTeacherAccount(Request $request)
     {
         // Validate the incoming request data
         $request->validate([
             'Teacher_ID' => 'required|unique:teachers,Teacher_ID',
             'Email' => 'required|email|unique:teachers,Email',
             'Password' => 'required|min:8',
             'FirstName' => 'required|string|max:255',
             'LastName' => 'required|string|max:255',
             'MiddleName' => 'nullable|string|max:255',
             'BirthDate' => 'required|date',
             'Sex' => 'required|in:M,F',
             'Position' => 'required|in:Admin,Coord,Teacher',
             'ContactNumber' => 'required|string|max:15',
             'Address' => 'required|string|max:255',
         ]);
 
         // Create a new teacher account
         $teacher = TeacherModel::create([
             'Teacher_ID' => $request->Teacher_ID,
             'Email' => $request->Email,
             'Password' => Hash::make($request->Password), // Hashing password
             'FirstName' => $request->FirstName,
             'LastName' => $request->LastName,
             'MiddleName' => $request->MiddleName,
             'BirthDate' => $request->BirthDate,
             'Sex' => $request->Sex,
             'Position' => $request->Position,
             'ContactNumber' => $request->ContactNumber,
             'Address' => $request->Address,
         ]);
 
         // Return a success response
         return response()->json([
             'message' => 'Teacher account created successfully.',
             'teacher' => $teacher,
         ], 201);
     }
}
