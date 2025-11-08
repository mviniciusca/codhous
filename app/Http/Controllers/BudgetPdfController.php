<?php

namespace App\Http\Controllers;

use App\Models\BudgetPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BudgetPdfController extends Controller
{
    /**
     * Download a budget PDF using a token
     *
     * @param Request $request
     * @param string $token
     * @return BinaryFileResponse
     */
    public function download(Request $request, string $token)
    {
        Log::info('PDF download requested', ['token' => $token]);

        try {
            // Find the PDF with the given token
            $pdf = BudgetPdf::where('download_token', $token)
                ->where('is_active', true)
                ->where('token_expires_at', '>', now())  // Verifica expiração do token
                ->first();

            if (! $pdf) {
                Log::warning('Invalid or expired token', ['token' => $token]);
                abort(404, 'Link inválido ou expirado. Por favor, gere um novo link.');
            }

            // Get the full path to the file
            $filePath = $pdf->getFullPath();

            // Check if the file exists
            if (! file_exists($filePath)) {
                Log::error('PDF file not found', [
                    'token'     => $token,
                    'path'      => $filePath,
                    'budget_id' => $pdf->budget_id,
                ]);
                abort(404, 'Arquivo não encontrado. Por favor, entre em contato com o suporte.');
            }

            // Get the budget for context
            $budget = $pdf->budget;

            // Record the download activity
            activity()
                ->performedOn($budget)
                ->withProperties([
                    'token'          => $token,
                    'download_count' => $pdf->download_count,
                    'filename'       => $pdf->filename,
                ])
                ->log('Downloaded PDF document for budget #'.$budget->code);

            // Increment download counter
            $pdf->incrementDownloadCount();

            Log::info('PDF download successful', [
                'token'          => $token,
                'budget_id'      => $pdf->budget_id,
                'download_count' => $pdf->download_count,
            ]);

            // Return the file for download with a nice filename
            return response()->download(
                $filePath,
                'Orcamento_'.$budget->code.'.pdf',
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            Log::error('Error in PDF download', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'Erro ao baixar o PDF. Por favor, tente novamente mais tarde.');
        }
    }
}
