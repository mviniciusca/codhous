<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetDownloadController extends Controller
{
    /**
     * Handle the download request for a budget PDF with a signed URL
     *
     * @param Request $request
     * @param int $budget
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function download(Request $request, $budget)
    {
        // Verify this is a valid signed URL
        if (! $request->hasValidSignature()) {
            abort(401, 'This link has expired or is invalid.');
        }

        // Find the budget
        $budget = Budget::findOrFail($budget);

        // Check if budget has a PDF document
        if (empty($budget->pdf_document)) {
            abort(404, 'PDF not found for this budget.');
        }

        // Build the full path - try both with and without trailing slash
        $pdfPath = $budget->pdf_document;
        $path = storage_path(env('PDF_DOWNLOAD_PATH', 'app/public/').$pdfPath);

        // If file doesn't exist, try with explicit path
        if (! file_exists($path)) {
            // Try with trailing slash
            $path = storage_path(rtrim(env('PDF_DOWNLOAD_PATH', 'app/public/'), '/').'/'.$pdfPath);

            // If still doesn't exist, try public path
            if (! file_exists($path)) {
                $path = public_path('storage/'.$pdfPath);
            }

            // If still doesn't exist, try storage path directly
            if (! file_exists($path)) {
                $path = storage_path('app/public/'.$pdfPath);
            }

            // If still doesn't exist, log and abort
            if (! file_exists($path)) {
                \Illuminate\Support\Facades\Log::error('PDF not found at any of the expected paths: '.$pdfPath);
                abort(404, 'PDF file not found. Please try regenerating it.');
            }
        }

        // Return file for download with nice name for the user
        return response()->download(
            $path,
            'Budget_'.$budget->code.'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
