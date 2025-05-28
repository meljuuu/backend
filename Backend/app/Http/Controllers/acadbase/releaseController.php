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
            $stampPath = storage_path('app/public/images/stamp.jpg');
            
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
            
            // Process all pages
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import the page
                $tplId = $pdf->importPage($pageNo);
                
                // Add the page
                $pdf->AddPage();
                $pdf->useTemplate($tplId);
                
                // Add the stamp image to each page with adjusted position
                $pdf->Image(
                    $stampPath,
                    120,  // X position (adjusted for better placement)
                    120,  // Y position (adjusted for better placement)
                    80    // Width (adjusted for better visibility)
                );
            }
            
            // Create a temporary file for the output
            $tempPath = storage_path('app/temp_' . time() . '.pdf');
            
            // Output to temporary file first
            $pdf->Output('F', $tempPath);
            
            // Verify the temporary file was created
            if (!file_exists($tempPath)) {
                throw new Exception('Failed to create temporary PDF file');
            }
            
            // Create stamped_pdfs directory if it doesn't exist
            $stampedDir = storage_path('app/public/stamped_pdfs');
            if (!file_exists($stampedDir)) {
                mkdir($stampedDir, 0755, true);
            }

            // Generate new filename for stamped PDF
            $stampedFilename = 'stamped_' . basename($student->pdf_storage);
            $stampedPath = $stampedDir . '/' . $stampedFilename;

            // Move the temporary file to the stamped_pdfs directory
            if (!rename($tempPath, $stampedPath)) {
                throw new Exception('Failed to save stamped PDF file');
            }

            // Update the student record with the new stamped PDF path
            $student->stamped_pdf_storage = 'public/stamped_pdfs/' . $stampedFilename;
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'PDF stamped successfully',
                'stamped_pdf_path' => $student->stamped_pdf_storage
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
