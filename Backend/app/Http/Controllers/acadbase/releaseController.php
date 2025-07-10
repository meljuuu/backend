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
                    110,  // X position
                    110,  // Y position
                    80    // Width
                );

                // Then add the date text on top of the image
                $pdf->SetFont('Arial', 'B', 18);
                $pdf->SetTextColor(0, 0, 0); // Black color
                $pdf->SetXY(125, 122); // x-axis, y-axis
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

    public function downloadStampedPdf($studentId)
    {
        try {
            $student = \App\Models\acadbase\MasterlistModel::findOrFail($studentId);
            
            if (!$student->stamped_pdf_storage) {
                return response()->json([
                    'success' => false,
                    'message' => 'No stamped PDF found for this student'
                ], 404);
            }

            $path = storage_path('app/' . $student->stamped_pdf_storage);
            
            if (!file_exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stamped PDF file not found on server'
                ], 404);
            }

            return response()->download($path, 'stamped_document.pdf');
        } catch (Exception $e) {
            \Log::error('Download Stamped PDF Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to download stamped PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus($studentId, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|string'
            ]);

            $student = \App\Models\acadbase\MasterlistModel::findOrFail($studentId);
            $student->status = $request->status;
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'Student status updated successfully',
                'status' => $student->status
            ]);
        } catch (Exception $e) {
            \Log::error('Update Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
