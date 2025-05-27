<?php

namespace App\Http\Controllers\acadbase;

use App\Http\Controllers\Controller;
use App\Models\acadbase\MasterlistModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterlistController extends Controller
{
    public function index()
    {
        $students = MasterlistModel::paginate(20);
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lrn' => 'required|unique:acadbase,lrn',
            'name' => 'required|string',
            'track' => 'required|string',
            'batch' => 'required|string',
            'curriculum' => 'required|string',
            'status' => 'required|in:Released,Unreleased,Not-Applicable,Dropped-Out',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('pdf_file');
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $path = $file->store('pdfs', 'public'); // Store in storage/app/public/pdfs
            $data['pdf_storage'] = $path;
        }

        $student = MasterlistModel::create($data);
        return response()->json($student, 201);
    }

    public function show($id)
    {
        $student = MasterlistModel::findOrFail($id);
        return response()->json($student);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'lrn' => 'required|unique:acadbase,lrn,' . $id,
            'name' => 'required|string',
            'track' => 'required|string',
            'batch' => 'required|string',
            'curriculum' => 'required|string',
            'status' => 'required|in:Released,Unreleased,Not-Applicable,Dropped-Out'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student = MasterlistModel::findOrFail($id);
        $student->update($request->all());
        return response()->json($student);
    }

    public function destroy($id)
    {
        $student = MasterlistModel::findOrFail($id);
        $student->delete();
        return response()->json(null, 204);
    }

    public function filter(Request $request)
    {
        $query = MasterlistModel::query();

        if ($request->filled('batch')) {
            $query->where('batch', $request->batch);
        }

        if ($request->filled('curriculum')) {
            $query->where('curriculum', $request->curriculum);
        }

        if ($request->filled('track')) {
            $query->where('track', $request->track);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20);
        
        return response()->json([
            'data' => $students->items(),
            'current_page' => $students->currentPage(),
            'total' => $students->total(),
            'per_page' => $students->perPage()
        ]);
    }
}
