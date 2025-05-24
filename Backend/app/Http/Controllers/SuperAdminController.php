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
use App\Models\LessonPlan;



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


    // Accept Student Tab
    public function acceptStudent($id)
    {
        $student = StudentModel::findOrFail($id);
        $student->Status = 'Accepted';
        $student->save();

        return response()->json([
            'message' => 'Student has been accepted.',
            'student' => $student
        ], 200);
    }

    // Decline Student Tab
    public function declineStudent(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $student = StudentModel::findOrFail($id);
        $student->Status = 'Declined';
        $student->comments = $request->input('comment'); // ✅ Save the rejection comment
        $student->save();

        return response()->json([
            'message' => 'Student has been declined.',
            'student' => $student
        ], 200);
    }

    //Lesson Plan Get All
    public function getAllLessonPlans()
    {
        $lessonPlans = LessonPlan::with('teacher')->get();

        $formatted = $lessonPlans->map(function ($lesson) {
            return [
                'LessonPlan_ID' => $lesson->LessonPlan_ID,
                'Teacher_ID' => $lesson->Teacher_ID,
                'TeacherName' => $lesson->teacher_name,  // Using the accessor here
                'lesson_plan_no' => $lesson->lesson_plan_no,
                'grade_level' => $lesson->grade_level,
                'section' => $lesson->section,
                'category' => $lesson->category,
                'link' => $lesson->link,
                'status' => $lesson->status,
                'comments' => $lesson->comments,
                'created_at' => $lesson->created_at,
                'updated_at' => $lesson->updated_at,
            ];
        });

        return response()->json($formatted);
    }

    //Lesson Plan Accept
    public function approveLessonPlan($id)
    {
        $lessonPlan = LessonPlan::find($id);

        if (!$lessonPlan) {
            return response()->json(['message' => 'Lesson Plan not found'], 404);
        }

        $lessonPlan->status = 'Approved';
        $lessonPlan->save();

        return response()->json(['message' => 'Lesson Plan approved successfully']);
    }

    // Lesson Plan Reject
    public function rejectLessonPlan($id)
    {
        $lessonPlan = LessonPlan::find($id);

        if (!$lessonPlan) {
            return response()->json(['message' => 'Lesson Plan not found'], 404);
        }

        $lessonPlan->status = 'Declined';
        $lessonPlan->save();

        return response()->json(['message' => 'Lesson Plan declined successfully']);
    }

    // Get Specific Lesson Plan 

    public function getLessonPlanById($id)
    {
        $lessonPlan = LessonPlan::find($id);

        if (!$lessonPlan) {
            return response()->json(['message' => 'Lesson plan not found'], 404);
        }

        return response()->json($lessonPlan);
    }




}
