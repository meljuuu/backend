<?php

namespace App\Http\Controllers\acadbase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use FPDF;
use setasign\Fpdi\Fpdi;
use Exception;

class ReleaseController extends Controller
{
    public function addImageOverlay(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:acadbase,id'
            ]);

            // Get the student record
            $student = \App\Models\acadbase\MasterlistModel::findOrFail($request->student_id);
            
            if (!$student->pdf_storage) {
                return response()->json([
                    'success' => false,
                    'message' => 'No PDF found for this student'
                ], 404);
            }

            // Get the PDF path - Modified to correctly access storage/app/public/pdfs
            $pdfPath = storage_path('app/public/pdfs/' . basename($student->pdf_storage));
            
            // Verify PDF exists
            if (!file_exists($pdfPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF file not found on server'
                ], 404);
            }

            // Get the stamp image path
            $stampPath = storage_path('app/public/images/stamp.png');
            
            // Verify stamp exists
            if (!file_exists($stampPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stamp image not found on server'
                ], 404);
            }

            // Create new PDF instance
            $pdf = new Fpdi();
            
            // Add the existing PDF
            $pageCount = $pdf->setSourceFile($pdfPath);
            
            // Import the first page
            $tplId = $pdf->importPage(1);
            
            // Add the page
            $pdf->AddPage();
            $pdf->useTemplate($tplId);
            
            // Add the stamp image (position can be adjusted here)
            $pdf->Image(
                $stampPath,
                50,  // X position
                50,  // Y position
                50   // Width (adjust as needed)
            );
            
            // Create a temporary file for the output
            $tempPath = storage_path('app/temp_' . time() . '.pdf');
            
            // Output to temporary file first
            $pdf->Output('F', $tempPath);
            
            // Verify the temporary file was created
            if (!file_exists($tempPath)) {
                throw new Exception('Failed to create temporary PDF file');
            }
            
            // Move the temporary file to the original location
            if (!rename($tempPath, $pdfPath)) {
                throw new Exception('Failed to update original PDF file');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'PDF updated successfully'
            ]);
            
        } catch (Exception $e) {
            \Log::error('PDF Overlay Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
