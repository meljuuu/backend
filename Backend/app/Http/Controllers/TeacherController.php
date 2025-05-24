<?php

namespace App\Http\Controllers;

use App\Models\TeacherModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Research;
use App\Models\SubjectModel as Subject;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum'); 
    // }

    public function getAll()
    {
        $teachers = TeacherModel::with('subjects')->get();

        // Optionally, format the response to include subject IDs for each teacher
        $data = $teachers->map(function ($teacher) {
            return [
                'teacher' => $teacher,
                'Subject_IDs' => $teacher->subjects->pluck('Subject_ID')->toArray(),
                'subjects' => $teacher->subjects,
            ];
        });

        return response()->json([
            'teachers' => $data
        ]);
    }
    
    public function getAllTeachers()
    {
        try {
            $teachers = TeacherModel::where('Position', 'Teacher')->get();

            return response()->json([
                'teachers' => $teachers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve teachers.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // public function createTeacherAccount(Request $request)
    // {
   
    //     $request->validate([
    //         'Teacher_ID' => 'required|unique:teachers,Teacher_ID',
    //         'Email' => 'required|email|unique:teachers,Email',
    //         'Password' => 'required|min:8',
    //         'FirstName' => 'required|string|max:255',
    //         'LastName' => 'required|string|max:255',
    //         'MiddleName' => 'nullable|string|max:255',
    //         'BirthDate' => 'required|date',
    //         'Sex' => 'required|in:M,F',
    //         'Position' => 'required|in:Admin,Coord,Teacher',
    //         'ContactNumber' => 'required|string|max:15',
    //         'Address' => 'required|string|max:255',
    //     ]);

    //     $authenticatedTeacher = Auth::user(); 
    //     if ($authenticatedTeacher->Position !== 'Admin') {
    //         return response()->json([
    //             'error' => 'Only Admins can create teacher accounts.',
    //         ], 403);
    //     }

    //     $teacher = TeacherModel::create([
    //         'Teacher_ID' => $request->Teacher_ID,
    //         'Email' => $request->Email,
    //         'Password' => Hash::make($request->Password),
    //         'FirstName' => $request->FirstName,
    //         'LastName' => $request->LastName,
    //         'MiddleName' => $request->MiddleName,
    //         'BirthDate' => $request->BirthDate,
    //         'Sex' => $request->Sex,
    //         'Position' => $request->Position,
    //         'ContactNumber' => $request->ContactNumber,
    //         'Address' => $request->Address,
    //     ]);

    //     return response()->json([
    //         'message' => 'Teacher account created successfully.',
    //         'teacher' => $teacher,
    //     ], 201);
    // }

public function createTeacherAccount(Request $request)
{
    $request->validate([
        'Email' => 'required|email|unique:teachers,Email',
        'Password' => 'required|min:8',
        'EmployeeNo' => 'required|string|unique:teachers,EmployeeNo',
        'Educational_Attainment' => 'required|string|max:255',
        'Teaching_Position' => 'required|string|max:255',
        'FirstName' => 'required|string|max:255',
        'LastName' => 'required|string|max:255',
        'MiddleName' => 'nullable|string|max:255',
        'Suffix' => 'nullable|string|max:255',
        'BirthDate' => 'required|date',
        'Sex' => 'required|in:M,F',
        'Position' => 'required|in:Admin,Book-keeping,Teacher,SuperAdmin',
        'ContactNumber' => 'required|string|max:15',
        'Address' => 'required|string|max:255',
        'Subject_IDs' => 'required_if:Position,Teacher|array|min:1|max:2',
        'Subject_IDs.*' => 'exists:subjects,Subject_ID',
    ]);

    $authenticatedTeacher = Auth::user(); 
    if (!in_array($authenticatedTeacher->Position, ['Admin', 'SuperAdmin'])) {
        return response()->json([
            'error' => 'Only Admins or SuperAdmins can create teacher accounts.',
        ], 403);
    }

    $teacher = TeacherModel::create([
        'Email' => $request->Email,
        'Password' => Hash::make($request->Password),
        'EmployeeNo' => $request->EmployeeNo,
        'FirstName' => $request->FirstName,
        'LastName' => $request->LastName,
        'MiddleName' => $request->MiddleName,
        'Suffix' => $request->Suffix,
        'Educational_Attainment' => $request->Educational_Attainment,
        'Teaching_Position' => $request->Teaching_Position,
        'BirthDate' => $request->BirthDate,
        'Sex' => $request->Sex,
        'Position' => $request->Position,
        'ContactNumber' => $request->ContactNumber,
        'Address' => $request->Address,
    ]);

    $subjects = [];
    if ($request->Position === 'Teacher') {
        $subjectIds = $request->Subject_IDs ?? [];
        if (!is_array($subjectIds)) {
            $subjectIds = [];
        }
        $subjects = count($subjectIds) > 0
            ? Subject::whereIn('Subject_ID', $subjectIds)->get()
            : collect();

        // Prepare and insert into teachers_subject table directly
        $now = now();
        $insertData = [];
        foreach ($subjects as $subject) {
            $insertData[] = [
                'teacher_id' => $teacher->Teacher_ID,
                'subject_id' => $subject->Subject_ID,
                'subject_code' => $subject->SubjectCode,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if (!empty($insertData)) {
            DB::table('teachers_subject')->insert($insertData);
        }
    }

    return response()->json([
        'message' => 'Teacher account created and subjects assigned successfully.',
        'teacher' => $teacher,
        'assigned_subjects' => $subjects,
    ], 201);
}

public function updateTeacherAccount(Request $request, $teacherId)
{
    try {
        $teacher = TeacherModel::findOrFail($teacherId);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'error' => 'Teacher not found.'
        ], 404);
    }

    $request->validate([
        'Email' => 'required|email|unique:teachers,Email,' . $teacherId . ',Teacher_ID',
        'Password' => 'nullable|min:8',
        'EmployeeNo' => 'required|string|unique:teachers,EmployeeNo,' . $teacherId . ',Teacher_ID',
        'Educational_Attainment' => 'required|string|max:255',
        'Teaching_Position' => 'required|string|max:255',
        'FirstName' => 'required|string|max:255',
        'LastName' => 'required|string|max:255',
        'MiddleName' => 'nullable|string|max:255',
        'Suffix' => 'nullable|string|max:255',
        'BirthDate' => 'required|date',
        'Sex' => 'required|in:M,F',
        'Position' => 'required|in:Admin,Book-Keeping,Teacher,SuperAdmin',
        'ContactNumber' => 'required|string|max:15',
        'Address' => 'required|string|max:255',
        'Subject_IDs' => 'required_if:Position,Teacher|array|min:1|max:2',
        'Subject_IDs.*' => 'exists:subjects,Subject_ID',
    ]);

    $authenticatedTeacher = Auth::user();
    if (!in_array($authenticatedTeacher->Position, ['Admin', 'SuperAdmin'])) {
        return response()->json([
            'error' => 'Only Admins or SuperAdmins can update teacher accounts.',
        ], 403);
    }

    $teacher->update([
        'Email' => $request->Email,
        'Password' => $request->filled('Password') ? Hash::make($request->Password) : $teacher->Password,
        'EmployeeNo' => $request->EmployeeNo,
        'FirstName' => $request->FirstName,
        'LastName' => $request->LastName,
        'MiddleName' => $request->MiddleName,
        'Suffix' => $request->Suffix,
        'Educational_Attainment' => $request->Educational_Attainment,
        'Teaching_Position' => $request->Teaching_Position,
        'BirthDate' => $request->BirthDate,
        'Sex' => $request->Sex,
        'Position' => $request->Position,
        'ContactNumber' => $request->ContactNumber,
        'Address' => $request->Address,
    ]);

    // Handle subject assignments
    if ($request->Position === 'Teacher') {
        $subjectIds = $request->Subject_IDs ?? [];
        if (!is_array($subjectIds)) {
            $subjectIds = [];
        }
        $subjects = count($subjectIds) > 0
            ? Subject::whereIn('Subject_ID', $subjectIds)->get()
            : collect();

        // Remove old assignments
        DB::table('teachers_subject')->where('teacher_id', $teacher->Teacher_ID)->delete();

        // Insert new assignments
        $now = now();
        $insertData = [];
        foreach ($subjects as $subject) {
            $insertData[] = [
                'teacher_id' => $teacher->Teacher_ID,
                'subject_id' => $subject->Subject_ID,
                'subject_code' => $subject->SubjectCode,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if (!empty($insertData)) {
            DB::table('teachers_subject')->insert($insertData);
        }
    } else {
        // Remove all subject assignments if not Teacher
        DB::table('teachers_subject')->where('teacher_id', $teacher->Teacher_ID)->delete();
        $subjects = [];
    }

    return response()->json([
        'message' => 'Teacher account updated successfully.',
        'teacher' => $teacher,
        'assigned_subjects' => $request->Position === 'Teacher' ? ($subjects ?? []) : [],
    ], 200);
}

    public function deleteTeacherAccount($id)
    {
        $teacher = TeacherModel::findOrFail($id);

        // Delete related subjects from pivot table
        \DB::table('teachers_subject')->where('teacher_id', $teacher->Teacher_ID)->delete();

        // Delete teacher record
        $teacher->delete();

        return response()->json([
            'message' => 'Teacher account and related subject assignments deleted successfully.'
        ]);
    }



    public function getAllPersonnel()
    {
        $teachers = TeacherModel::all();
        return response()->json($teachers);
    }

    public function getProfile(Request $request)
    {
        $teacher = $request->user()->load('researches');
        return response()->json([
            'teacher' => [
                'firstName' => $teacher->FirstName,
                'lastName' => $teacher->LastName,
                'middleName' => $teacher->MiddleName,
                'employeeNo' => $teacher->EmployeeNo,
                'position' => $teacher->Position,
                'email' => $teacher->Email,
                'contactNumber' => $teacher->ContactNumber,
                'address' => $teacher->Address,
                'avatar' => $teacher->Avatar,
                'research' => $teacher->researches->map(function($research) {
                    return [
                        'Research_ID' => $research->Research_ID,
                        'Title' => $research->Title,
                        'Abstract' => $research->Abstract,
                        'created_at' => $research->created_at
                    ];
                }),
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $teacher = $request->user();
        
        $validatedData = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'employeeNo' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactNumber' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $teacher->update([
            'FirstName' => $validatedData['firstName'],
            'LastName' => $validatedData['lastName'],
            'MiddleName' => $validatedData['middleName'],
            'EmployeeNo' => $validatedData['employeeNo'],
            'Email' => $validatedData['email'],
            'ContactNumber' => $validatedData['contactNumber'],
            'Address' => $validatedData['address'],
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'teacher' => [
                'firstName' => $teacher->FirstName,
                'lastName' => $teacher->LastName,
                'middleName' => $teacher->MiddleName,
                'employeeNo' => $teacher->EmployeeNo,
                'email' => $teacher->Email,
                'contactNumber' => $teacher->ContactNumber,
                'address' => $teacher->Address,
            ]
        ]);
    }

    public function addResearch(Request $request) {
        $validated = $request->validate([
            'Title' => 'required|string|max:255',
            'Abstract' => 'required|string',
        ]);

        $research = auth()->user()->researches()->create([
            'Title' => $validated['Title'],
            'Abstract' => $validated['Abstract']
        ]);

        return response()->json($research, 201);
    }

    

}
