<?php

namespace App\Http\Controllers;

use App\Models\SubjectGradeModel;
use App\Models\StudentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GradingController extends Controller
{
    /**
     * Get grades for a specific subject
     */
    public function getSubjectGrades($subjectId)
    {
        try {
            $grades = SubjectGradeModel::with(['student' => function($query) {
                    $query->select('Student_ID', 'FirstName', 'MiddleName', 'LastName', 'LRN', 'Sex', 'BirthDate', 'Curriculum');
                }, 'teacher', 'subject'])
                ->where('Subject_ID', $subjectId)
                ->get();

            // Transform the data to match frontend expectations
            $grades = $grades->map(function($grade) {
                return [
                    'student_id' => $grade->student->Student_ID,
                    'firstName' => $grade->student->FirstName,
                    'middleName' => $grade->student->MiddleName ?? '',
                    'lastName' => $grade->student->LastName,
                    'lrn' => $grade->student->LRN,
                    'sex' => $grade->student->Sex,
                    'birthDate' => $grade->student->BirthDate,
                    'curriculum' => $grade->student->Curriculum,
                    'grades' => [
                        'first' => $grade->Q1,
                        'second' => $grade->Q2,
                        'third' => $grade->Q3,
                        'fourth' => $grade->Q4,
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $grades
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSubjectGrades: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch grades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit grades for multiple students
     */
    public function submitGrades(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'grades' => 'required|array',
                'grades.*.Student_ID' => 'required|exists:students,Student_ID',
                'grades.*.Subject_ID' => 'required|exists:subjects,Subject_ID',
                'grades.*.Teacher_ID' => 'required|exists:teachers,Teacher_ID',
                'grades.*.Q1' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q2' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q3' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q4' => 'nullable|numeric|min:0|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            foreach ($request->grades as $gradeData) {
                // Calculate final grade if all quarters are present
                $finalGrade = null;
                if ($gradeData['Q1'] !== null && $gradeData['Q2'] !== null && 
                    $gradeData['Q3'] !== null && $gradeData['Q4'] !== null) {
                    $finalGrade = round(($gradeData['Q1'] + $gradeData['Q2'] + $gradeData['Q3'] + $gradeData['Q4']) / 4, 2);
                }

                // Determine remarks based on final grade
                $remarks = null;
                if ($finalGrade !== null) {
                    $remarks = $finalGrade >= 75 ? 'Passed' : 'Failed';
                }

                // Update or create grade record
                SubjectGradeModel::updateOrCreate(
                    [
                        'Student_ID' => $gradeData['Student_ID'],
                        'Subject_ID' => $gradeData['Subject_ID'],
                    ],
                    [
                        'Teacher_ID' => $gradeData['Teacher_ID'],
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
            \Log::error('Error in submitGrades: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit grades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get grades for a specific student
     */
    public function getStudentGrades($studentId, $subjectId)
    {
        try {
            $grades = SubjectGradeModel::with(['subject', 'teacher'])
                ->where('Student_ID', $studentId)
                ->where('Subject_ID', $subjectId)
                ->first();

            if (!$grades) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No grades found for this student'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $grades
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getStudentGrades: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student grades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a specific grade
     */
    public function updateGrade(Request $request, $gradeId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Q1' => 'nullable|numeric|min:0|max:100',
                'Q2' => 'nullable|numeric|min:0|max:100',
                'Q3' => 'nullable|numeric|min:0|max:100',
                'Q4' => 'nullable|numeric|min:0|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $grade = SubjectGradeModel::findOrFail($gradeId);
            
            // Update quarter grades
            $grade->Q1 = $request->Q1;
            $grade->Q2 = $request->Q2;
            $grade->Q3 = $request->Q3;
            $grade->Q4 = $request->Q4;

            // Recalculate final grade if all quarters are present
            if ($request->Q1 !== null && $request->Q2 !== null && 
                $request->Q3 !== null && $request->Q4 !== null) {
                $grade->FinalGrade = round(($request->Q1 + $request->Q2 + $request->Q3 + $request->Q4) / 4, 2);
                $grade->Remarks = $grade->FinalGrade >= 75 ? 'Passed' : 'Failed';
            } else {
                $grade->FinalGrade = null;
                $grade->Remarks = null;
            }

            $grade->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Grade updated successfully',
                'data' => $grade
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update grade: ' . $e->getMessage()
            ], 500);
        }
    }
}