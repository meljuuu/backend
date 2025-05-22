<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\ClassesModel;
use App\Models\User;
use App\Models\TeacherModel;
use App\Models\SchoolYearModel;
use App\Models\StudentModel;
use App\Models\SubjectGradeModel;


class SuperAdminController extends Controller
{
    
    //Classes API
    public function getAllWithStudentCount()
        {
            return ClassesModel::select(
                    'classes.*',
                    DB::raw('(SELECT COUNT(*) FROM students 
                            WHERE students.Track = classes.Track 
                            AND students.Curriculum = classes.Curriculum) as student_added')
                )
                ->with(['adviser:Teacher_ID,FirstName,LastName,MiddleName', 'schoolYear'])
                ->get();
        }


    //Get All the data from "Student Table"
    public function getAllStudentsData()
        {
            $students = StudentModel::all(); 

            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        }

    //Student Modal for "Individual Registration Form"
    public function getStudentById($id)
    {
        $student = StudentModel::find($id);

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $student,
        ]);
    }





}
