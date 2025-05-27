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
                'grades.*.Class_ID' => 'required|exists:classes,Class_ID',
                'grades.*.Q1' => 'nullable|numeric|min:0|max:100',
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

                try {
                    if ($existingGrade) {
                        $updated = $existingGrade->update([
                            'Class_ID' => $gradeData['Class_ID'],
                            'Q1' => $gradeData['Q1'] ?? $existingGrade->Q1,
                            'Q2' => $gradeData['Q2'] ?? $existingGrade->Q2,
                            'Q3' => $gradeData['Q3'] ?? $existingGrade->Q3,
                            'Q4' => $gradeData['Q4'] ?? $existingGrade->Q4,
                            'FinalGrade' => $gradeData['FinalGrade'] ?? $existingGrade->FinalGrade,
                            'Remarks' => $gradeData['Remarks'] ?? $existingGrade->Remarks,
                            'Teacher_ID' => $gradeData['Teacher_ID']
                        ]);
                        if (!$updated) {
                            throw new \Exception("Failed to update grade for student {$gradeData['Student_ID']}");
                        }
                    } else {
                        // Create new grade
                        $created = SubjectGradeModel::create([
                            'Class_ID' => $gradeData['Class_ID'],
                            'Student_ID' => $gradeData['Student_ID'],
                            'Subject_ID' => $gradeData['Subject_ID'],
                            'Teacher_ID' => $gradeData['Teacher_ID'],
                            'Q1' => $gradeData['Q1'],
                            'Q2' => $gradeData['Q2'],
                            'Q3' => $gradeData['Q3'],
                            'Q4' => $gradeData['Q4'],
                            'FinalGrade' => $gradeData['FinalGrade'],
                            'Remarks' => $gradeData['Remarks']
                        ]);
                        if (!$created) {
                            throw new \Exception("Failed to create grade for student {$gradeData['Student_ID']}");
                        }
                    }

                    Log::info('Incoming grade data:', $gradeData);
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error('Grade save failed:', ['error' => $e->getMessage(), 'data' => $gradeData]);
                    $errorCount++;
                    continue;
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