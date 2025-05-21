<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    // Display a listing of subjects
    public function index()
    {
        $subjects = Subject::all();
        return response()->json($subjects);
    }

    // Store a newly created subject in storage
    public function store(Request $request)
    {
        $request->validate([
            'SubjectName' => 'required|string|max:255',
            'SubjectCode' => 'required|integer|unique:subjects,SubjectCode',
        ]);

        $subject = Subject::create([
            'SubjectName' => $request->SubjectName,
            'SubjectCode' => $request->SubjectCode,
        ]);

        return response()->json($subject, 201);
    }

    // Display the specified subject
    public function show($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        return response()->json($subject);
    }

    // Update the specified subject in storage
    public function update(Request $request, $id)
    {
        $subject = Subject::find($id);

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
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
