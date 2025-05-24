<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubjectModel;
use App\Models\SubjectGradeModel;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    // Display a listing of subjects
    public function getAll()
    {
        $subjects = SubjectModel::with(['teacherSubjects.teacher'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $subjects->map(function($subject) {
                return [
                    'id' => $subject->Subject_ID,
                    'name' => $subject->SubjectName,
                    'code' => $subject->SubjectCode,
                    'teachers' => $subject->teacherSubjects->map(function($ts) {
                        return [
                            'id' => $ts->teacher->Teacher_ID,
                            'name' => $ts->teacher->FirstName . ' ' . $ts->teacher->LastName
                        ];
                    })->unique('id')->values()
                ];
            })
        ]);
    }

    // Store a newly created subject in storage
    public function store(Request $request)
    {
        $request->validate([
            'SubjectName' => 'required|string|max:255',
            'SubjectCode' => 'required|integer|unique:subjects,SubjectCode',
        ]);

        $subject = SubjectModel::create([
            'SubjectName' => $request->SubjectName,
            'SubjectCode' => $request->SubjectCode,
        ]);

        return response()->json($subject, 201);
    }

    // Display the specified subject
    public function show($id)
    {
        $subject = SubjectModel::find($id);

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        return response()->json($subject);
    }

    // Update the specified subject in storage
    public function update(Request $request, $id)
    {
        $subject = SubjectModel::find($id);

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        $request->validate([
            'SubjectName' => 'sometimes|required|string|max:255',
            'SubjectCode' => "sometimes|required|integer|unique:subjects,SubjectCode,$id,Subject_ID",
        ]);

        $subject->update($request->only(['SubjectName', 'SubjectCode']));

        return response()->json($subject);
    }

    // Remove the specified subject from storage
    public function destroy($id)
    {
        $subject = SubjectModel::find($id);

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }

    // Add new method to submit grades
    public function submitGrades(Request $request)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*.Student_ID' => 'required|exists:students,Student_ID',
            'grades.*.Teacher_ID' => 'required|exists:teachers,Teacher_ID',
            'grades.*.Subject_ID' => 'required|exists:subjects,Subject_ID',
            'grades.*.Q1' => 'nullable|integer|min:0|max:100',
            'grades.*.Q2' => 'nullable|integer|min:0|max:100',
            'grades.*.Q3' => 'nullable|integer|min:0|max:100',
            'grades.*.Q4' => 'nullable|integer|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->grades as $gradeData) {
                // Calculate final grade if all quarters are present
                $finalGrade = null;
                if ($gradeData['Q1'] !== null && $gradeData['Q2'] !== null && 
                    $gradeData['Q3'] !== null && $gradeData['Q4'] !== null) {
                    $finalGrade = round(($gradeData['Q1'] + $gradeData['Q2'] + 
                                      $gradeData['Q3'] + $gradeData['Q4']) / 4);
                }

                // Determine remarks based on final grade
                $remarks = null;
                if ($finalGrade !== null) {
                    $remarks = $finalGrade >= 75 ? 'Passed' : 'Failed';
                }

                // Create or update the grade record
                SubjectGradeModel::updateOrCreate(
                    [
                        'Student_ID' => $gradeData['Student_ID'],
                        'Teacher_ID' => $gradeData['Teacher_ID'],
                        'Subject_ID' => $gradeData['Subject_ID'],
                    ],
                    [
                        'Q1' => $gradeData['Q1'],
                        'Q2' => $gradeData['Q2'],
                        'Q3' => $gradeData['Q3'],
                        'Q4' => $gradeData['Q4'],
                        'FinalGrade' => $finalGrade,
                        'Remarks' => $remarks,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Grades submitted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit grades: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add method to get grades for a subject
    public function getGrades($subjectId)
    {
        $grades = SubjectGradeModel::with(['student', 'teacher'])
            ->where('Subject_ID', $subjectId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $grades
        ]);
    }
}
