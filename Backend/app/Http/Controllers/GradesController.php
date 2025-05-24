<?php

namespace App\Http\Controllers;

use App\Models\SubjectGradeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GradesController extends Controller
{
    public function bulkStore(Request $request)
    {
        try {
            $request->validate([
                'grades' => 'required|array',
                'grades.*.Student_ID' => 'required|exists:students,Student_ID',
                'grades.*.Subject_ID' => 'required|exists:subjects,Subject_ID',
                'grades.*.Teacher_ID' => 'required|exists:teachers,Teacher_ID',
                'grades.*.Q1' => 'nullable|numeric|min:60|max:100',
                'grades.*.Q2' => 'nullable|numeric|min:60|max:100',
                'grades.*.Q3' => 'nullable|numeric|min:60|max:100',
                'grades.*.Q4' => 'nullable|numeric|min:60|max:100',
                'grades.*.FinalGrade' => 'nullable|numeric|min:60|max:100',
                'grades.*.Remarks' => 'nullable|string',
            ]);

            $successCount = 0;
            $errorCount = 0;

            foreach ($request->grades as $gradeData) {
                // Check if grade entry already exists
                $existingGrade = SubjectGradeModel::where('Student_ID', $gradeData['Student_ID'])
                    ->where('Subject_ID', $gradeData['Subject_ID'])
                    ->first();

                if ($existingGrade) {
                    // Update existing grade
                    $existingGrade->update([
                        'Q1' => $gradeData['Q1'] ?? $existingGrade->Q1,
                        'Q2' => $gradeData['Q2'] ?? $existingGrade->Q2,
                        'Q3' => $gradeData['Q3'] ?? $existingGrade->Q3,
                        'Q4' => $gradeData['Q4'] ?? $existingGrade->Q4,
                        'FinalGrade' => $gradeData['FinalGrade'] ?? $existingGrade->FinalGrade,
                        'Remarks' => $gradeData['Remarks'] ?? $existingGrade->Remarks,
                        'Teacher_ID' => $gradeData['Teacher_ID']
                    ]);
                    $successCount++;
                } else {
                    // Create new grade
                    SubjectGradeModel::create($gradeData);
                    $successCount++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "$successCount grades have been saved successfully.",
                'errors' => $errorCount > 0 ? "$errorCount grades failed to save." : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving grades: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save grades: ' . $e->getMessage()
            ], 500);
        }
    }
}