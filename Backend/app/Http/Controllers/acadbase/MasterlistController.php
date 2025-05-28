<?php

namespace App\Http\Controllers\acadbase;

use App\Http\Controllers\Controller;
use App\Models\acadbase\MasterlistModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\acadbase\CsvModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

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
            'lrn' => 'nullable|unique:acadbase,lrn|regex:/^[0-9]+$/',
            'name' => 'nullable|string',
            'track' => 'nullable|in:SPJ,BEC,SPA',
            'batch' => 'nullable|regex:/^\d{4}-\d{4}$/',
            'curriculum' => 'nullable|in:JHS,SHS',
            'status' => 'nullable|in:Released,Unreleased,Not-Applicable,Dropped-Out',
            'birthdate' => 'nullable|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('pdf_file');
        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('pdfs', 'public');
            $data['pdf_storage'] = $path;
            \Log::info("PDF Path: " . $data['pdf_storage']);
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
            'track' => 'required|in:SPJ,BEC,SPA',
            'batch' => 'required|regex:/^\d{4}-\d{4}$/',
            'curriculum' => 'required|in:JHS,SHS',
            'status' => 'required|in:Released,Unreleased,Not-Applicable,Dropped-Out',
            'birthdate' => 'required|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student = MasterlistModel::findOrFail($id);
        
        // Handle PDF file upload if present
        if ($request->hasFile('pdf_file')) {
            // Delete old PDF if exists
            if ($student->pdf_storage) {
                Storage::disk('public')->delete($student->pdf_storage);
            }
            
            $path = $request->file('pdf_file')->store('pdfs', 'public');
            $student->pdf_storage = $path;
        }

        // Update other fields
        $student->lrn = $request->lrn;
        $student->name = $request->name;
        $student->track = $request->track;
        $student->batch = $request->batch;
        $student->curriculum = $request->curriculum;
        $student->status = $request->status;
        $student->birthdate = $request->birthdate;
        $student->faculty_name = $request->faculty_name;
        
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
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

    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'xlsx' || $extension === 'xls') {
                // For Excel files, we'll need to convert to CSV first
                $csvFile = tempnam(sys_get_temp_dir(), 'csv_');
                $handle = fopen($csvFile, 'w');
                
                // Read Excel file and write to CSV
                $reader = IOFactory::createReader('Xlsx');
                $spreadsheet = $reader->load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                
                // Write headers
                $headers = [];
                foreach ($worksheet->getRowIterator(1, 1) as $row) {
                    foreach ($row->getCellIterator() as $cell) {
                        $headers[] = $cell->getValue();
                    }
                }
                // Add status header if it doesn't exist
                if (!in_array('status', array_map('strtolower', $headers))) {
                    $headers[] = 'status';
                }
                fputcsv($handle, $headers);
                
                // Write data rows
                foreach ($worksheet->getRowIterator(2) as $row) {
                    $rowData = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    // Force status to Not-Applicable
                    $rowData[] = 'Not-Applicable';
                    fputcsv($handle, $rowData);
                }
                
                fclose($handle);
                
                // Import the CSV
                $result = CsvModel::importFromFile(new \Illuminate\Http\UploadedFile($csvFile, 'temp.csv'));
                
                // Clean up
                unlink($csvFile);

                return response()->json([
                    'message' => 'Students imported successfully',
                    'stats' => $result
                ], 201);
            } else {
                // For CSV files, import directly
                $result = CsvModel::importFromFile($file);
                return response()->json([
                    'message' => 'Students imported successfully',
                    'stats' => $result
                ], 201);
            }
        } catch (\Exception $e) {
            \Log::error('File import error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to process file',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
