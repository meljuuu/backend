<?php
// filepath: c:\Users\PC\Desktop\COMEX-REAL\RESILIENT-Backend\Backend\app\Http\Controllers\DashboardController.php
namespace App\Http\Controllers;
use App\Models\TeacherModel;
use App\Models\StudentModel;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function getAdvisoryStats(Request $request)
{
    $teacher = $request->user();

    if (!$teacher) {
        return response()->json(['error' => 'Teacher not found'], 404);
    }

    // Fetch only advisory classes for the authenticated teacher
    $advisoryClasses = $teacher->classes()->wherePivot('isAdvisory', 1)->get();

    if ($advisoryClasses->isEmpty()) {
        return response()->json([
            'advisoryClasses' => [],
            'total' => 0,
            'male' => 0,
            'female' => 0,
        ]);
    }

    // Collect all students from advisory classes and ensure uniqueness
    $students = $advisoryClasses->flatMap(function ($class) {
        return $class->students;
    })->unique('Student_ID'); // Ensure unique students

    // Calculate stats
    $total = $students->count();
    $male = $students->where('Sex', 'M')->count();
    $female = $students->where('Sex', 'F')->count();

    return response()->json([
        'advisoryClasses' => $advisoryClasses,
        'total' => $total,
        'male' => $male,
        'female' => $female,
    ]);
}

    public function getSubjectClasses()
    {
        // Fetch subject classes from the database
        $classes = [
            ['count' => 25, 'name' => 'Math', 'subject' => 'Algebra'],
            ['count' => 20, 'name' => 'Science', 'subject' => 'Biology'],
        ];
        return response()->json($classes);
    }

    public function getGradeSummary()
    {
        // Fetch grade summary from the database
        $summary = [
            '90-100' => 10,
            '85-89' => 15,
            '80-84' => 20,
            '75-79' => 5,
            'Below 75' => 2,
        ];
        return response()->json($summary);
    }

    public function getRecentGrades()
    {
        // Fetch recent grades from the database
        $grades = [
            ['lrn' => '12345', 'name' => 'John Doe', 'grade' => 95, 'remarks' => 'Excellent'],
            ['lrn' => '67890', 'name' => 'Jane Smith', 'grade' => 88, 'remarks' => 'Very Good'],
        ];
        return response()->json($grades);
    }
}