<?php

namespace App\Http\Controllers;

use App\Models\StudentClassModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StudentClassController extends Controller
{
   
    public function index()
    {
        $list = StudentClassModel::with(['student', 'class', 'schoolYear', 'teacher', 'adviser', 'teacherSubject'])
            ->get();

        return response()->json($list);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,Student_ID',
            'class_id' => 'required|exists:classes,Class_ID',
            'class_name' => 'nullable|string',
            'sy_id' => 'required|exists:school_years,SY_ID',
            'adviser_id' => 'nullable|exists:teachers,Teacher_ID',
            'teacher_subject_ids' => 'required|array',
            'teacher_subject_ids.*' => 'integer|exists:teachers_subject,id',
            'is_advisory' => 'boolean',
        ]);
    
        $studentClassRecords = [];
    
        foreach ($request->student_ids as $studentId) {
            $studentClass = StudentClassModel::create([
                'Student_ID' => $studentId,
                'Class_ID' => $request->class_id,
                'ClassName' => $request->class_name,
                'SY_ID' => $request->sy_id,
                'Adviser_ID' => $request->adviser_id,
                'isAdvisory' => $request->is_advisory ?? false,
            ]);
    
            // Attach teacher_subjects via pivot
            $studentClass->teacherSubjects()->attach($request->teacher_subject_ids);
    
            $studentClassRecords[] = $studentClass;
        }
    
        // ✅ Update class status from 'incomplete' to 'pending'
        DB::table('classes')
            ->where('Class_ID', $request->class_id)
            ->where('Status', 'incomplete')
            ->update(['Status' => 'pending']);
    
        return response()->json([
            'message' => 'Student classes and subject assignments created successfully.',
            'data' => $studentClassRecords
        ]);
    }
    
    
    public function show($id)
    {
        $item = StudentClass::with(['student', 'class', 'schoolYear', 'teacher', 'adviser', 'teacherSubject'])
            ->findOrFail($id);

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Student_ID'        => ['sometimes', 'exists:students,Student_ID'],
            'Class_ID'          => ['sometimes', 'exists:classes,Class_ID'],
            'ClassName'         => ['sometimes', 'string', 'max:255'],
            'SY_ID'             => ['sometimes', 'exists:school_years,SY_ID'],
            'Teacher_ID'        => ['nullable', 'exists:teachers,Teacher_ID'],
            'Adviser_ID'        => ['nullable', 'exists:teachers,Teacher_ID'],
            'TeacherSubject_ID' => ['nullable', 'exists:teachers_subject,id'],
            'isAdvisory'        => ['boolean'],
            'Status'            => ['sometimes', Rule::in(['Pending', 'Accepted', 'Declined', 'Incomplete'])],
        ]);

        $item = StudentClass::findOrFail($id);
        $item->update($validated);

        return response()->json([
            'message' => 'StudentClass updated',
            'data'    => $item
        ]);
    }

    public function destroy($id)
    {
        $item = StudentClass::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'StudentClass deleted']);
    }
}
