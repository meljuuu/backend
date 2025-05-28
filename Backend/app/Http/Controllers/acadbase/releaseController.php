<?php

namespace App\Http\Controllers\acadbase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use FPDF;
use setasign\Fpdi\Fpdi;
use Exception;
use Carbon\Carbon;

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

            // Get the PDF path
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
            
            // Get current date and time with timezone
            $now = Carbon::now()->setTimezone('Asia/Manila'); // Set to Philippine timezone
            $formattedDate = $now->format('F d, Y h:i A');

            // Process all pages
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import the page
                $tplId = $pdf->importPage($pageNo);
                
                // Add the page
                $pdf->AddPage();
                $pdf->useTemplate($tplId);
                
                // First add the stamp image
                $pdf->Image(
                    $stampPath,
                    120,  // X position
                    120,  // Y position
                    80    // Width
                );

                // Then add the date text on top of the image
                $pdf->SetFont('Arial', 'B', 18);
                $pdf->SetTextColor(0, 0, 0); // Black color
                $pdf->SetXY(120, 125); // Moved down from 120 to 130
                $pdf->Cell(80, 10, $formattedDate, 0, 1, 'C');
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

            // Update the student record with the new stamped PDF path and dates
            $student->stamped_pdf_storage = 'public/stamped_pdfs/' . $stampedFilename;
            $student->furnished_date = $now->format('Y-m-d H:i:s');
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'PDF stamped successfully',
                'stamped_pdf_path' => $student->stamped_pdf_storage,
                'furnished_date' => $student->furnished_date
            ]);
            
        } catch (Exception $e) {
            \Log::error('PDF Overlay Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStampedStatus($studentId)
    {
        try {
            $student = \App\Models\acadbase\MasterlistModel::findOrFail($studentId);
            
            return response()->json([
                'success' => true,
                'has_stamped_pdf' => !empty($student->stamped_pdf_storage),
                'stamped_pdf_path' => $student->stamped_pdf_storage,
                'furnished_date' => $student->furnished_date
            ]);
        } catch (Exception $e) {
            \Log::error('Check Stamped Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check stamped status: ' . $e->getMessage()
            ], 500);
        }
    }
}
