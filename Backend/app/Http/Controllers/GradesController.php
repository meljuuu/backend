<?php

namespace App\Http\Controllers;

use App\Models\SubjectGradeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GradesController extends Controller
{
    public function bulkStore(Request $request)
    {
        try {
            \Log::info('Received grades data:', $request->all());

            $request->validate([
                'grades' => 'required|array',
                'grades.*.Student_ID' => 'required|exists:students,Student_ID',
                'grades.*.Subject_ID' => 'required|exists:subjects,Subject_ID',
                'grades.*.Teacher_ID' => 'required|exists:teachers,Teacher_ID',
                'grades.*.Class_ID' => 'required|exists:classes,Class_ID',
                'grades.*.Q1' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q2' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q3' => 'nullable|numeric|min:0|max:100',
                'grades.*.Q4' => 'nullable|numeric|min:0|max:100',
                'grades.*.FinalGrade' => 'nullable|numeric|min:0|max:100',
                'grades.*.Remarks' => 'nullable|string',
            ]);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($request->grades as $gradeData) {
                try {
                    \Log::info('Processing grade:', $gradeData);

                    // Check if grade entry already exists
                    $existingGrade = SubjectGradeModel::where('Student_ID', $gradeData['Student_ID'])
                        ->where('Subject_ID', $gradeData['Subject_ID'])
                        ->first();

                    if ($existingGrade) {
                        // Update existing grade
                        $existingGrade->update([
                            'Class_ID' => $gradeData['Class_ID'],
                            'Teacher_ID' => $gradeData['Teacher_ID'],
                            'Q1' => $gradeData['Q1'],
                            'Q2' => $gradeData['Q2'],
                            'Q3' => $gradeData['Q3'],
                            'Q4' => $gradeData['Q4'],
                            'FinalGrade' => $gradeData['FinalGrade'],
                            'Remarks' => $gradeData['Remarks'],
                            'Status' => 'Pending'
                        ]);
                        \Log::info('Updated existing grade:', $existingGrade->toArray());
                    } else {
                        // Create new grade
                        $newGrade = SubjectGradeModel::create([
                            'Class_ID' => $gradeData['Class_ID'],
                            'Student_ID' => $gradeData['Student_ID'],
                            'Subject_ID' => $gradeData['Subject_ID'],
                            'Teacher_ID' => $gradeData['Teacher_ID'],
                            'Q1' => $gradeData['Q1'],
                            'Q2' => $gradeData['Q2'],
                            'Q3' => $gradeData['Q3'],
                            'Q4' => $gradeData['Q4'],
                            'FinalGrade' => $gradeData['FinalGrade'],
                            'Remarks' => $gradeData['Remarks'],
                            'Status' => 'Pending'
                        ]);
                        \Log::info('Created new grade:', $newGrade->toArray());
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Failed to save grade for student {$gradeData['Student_ID']}: {$e->getMessage()}";
                    \Log::error('Error saving grade:', ['error' => $e->getMessage(), 'gradeData' => $gradeData]);
                    continue;
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "$successCount grades have been saved successfully.",
                'errors' => $errorCount > 0 ? "$errorCount grades failed to save." : null,
                'detailed_errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving grades: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save grades: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getGradesBySubjectAndClass($subjectId, $classId)
    {
        try {
            $grades = SubjectGradeModel::where('Subject_ID', $subjectId)
                ->where('Class_ID', $classId)
                ->with([
                    'student' => function($query) {
                        $query->select('Student_ID', 'FirstName', 'LastName', 'MiddleName', 'LRN', 'sex', 'birthDate');
                    },
                    'subject',
                    'teacher'
                ])
                ->get()
                ->map(function ($grade) {
                    // Attach grades and calculated fields
                    return [
                        'Grade_ID'    => $grade->Grade_ID,
                        'Student_ID'  => $grade->Student_ID,
                        'Q1'          => $grade->Q1,
                        'Q2'          => $grade->Q2,
                        'Q3'          => $grade->Q3,
                        'Q4'          => $grade->Q4,
                        'FinalGrade'  => $grade->FinalGrade,
                        'Remarks'     => $grade->Remarks,
                        'Status'      => $grade->Status,
                        'comments'    => $grade->comments,
                        'student'     => $grade->student,
                        'grades'      => [
                            'first'  => $grade->Q1,
                            'second' => $grade->Q2,
                            'third'  => $grade->Q3,
                            'fourth' => $grade->Q4,
                        ]
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data'   => $grades
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching grades: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch grades.'
            ], 500);
        }
    }
}