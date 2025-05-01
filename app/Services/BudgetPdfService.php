<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetPdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class BudgetPdfService
{
    /**
     * Storage disk to use for PDFs
     *
     * @var string
     */
    protected $disk;

    /**
     * The storage path for PDF files
     *
     * @var string
     */
    protected $storagePath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->disk = 'public';
        $this->storagePath = 'budgets/pdf/';
    }

    /**
     * Generate a PDF for the budget and store it
     *
     * @param Budget|int $budget Budget model or ID
     * @param bool $forceRegenerate Whether to force regeneration if PDF already exists
     * @return BudgetPdf|null The PDF model or null if generation failed
     */
    public function generatePdf($budget, bool $forceRegenerate = false): ?BudgetPdf
    {
        // Get the budget model if an ID was passed
        if (! ($budget instanceof Budget)) {
            $budget = Budget::findOrFail($budget);
        }

        // Check if we already have a recent PDF and don't need to regenerate
        if (! $forceRegenerate) {
            $existingPdf = $budget->latestPdf();
            if ($existingPdf && $existingPdf->fileExists() && $existingPdf->created_at->isAfter(now()->subMinutes(30))) {
                return $existingPdf;
            }
        }

        try {
            // Load the budget with all relationships for proper PDF generation
            $budget = Budget::with(['products'])->findOrFail($budget->id);

            // Convert the budget to the complete state format expected by PdfGenerator
            $state = $this->convertBudgetToState($budget);

            // Use the existing PdfGenerator to generate the PDF
            $pdfGenerator = new PdfGenerator($state);
            $filePath = $pdfGenerator->savePath();

            // Generate the PDF using the existing generator
            $pdfGenerator->generate(false); // false = do not download, just generate

            // Check if file was created successfully
            if (! file_exists($filePath)) {
                throw new \Exception('PDF file was not created at: '.$filePath);
            }

            // Generate filename for storage
            $filename = $this->generateFilename($budget);
            $relativePath = $this->storagePath.$filename;
            $fullPath = Storage::disk($this->disk)->path($relativePath);

            // Copy the generated file to our storage location
            if (! copy($filePath, $fullPath)) {
                throw new \Exception('Failed to copy PDF from temporary location to final storage');
            }

            // Log success
            Log::info('PDF generated successfully', [
                'budget_id' => $budget->id,
                'path'      => $relativePath,
                'fullPath'  => $fullPath,
            ]);

            // Create a new BudgetPdf record
            $budgetPdf = BudgetPdf::create([
                'budget_id' => $budget->id,
                'filename'  => $filename,
                'path'      => $relativePath,
                'is_active' => true,
            ]);

            // Generate a download token
            $budgetPdf->generateDownloadToken();

            // Record the activity
            activity()
                ->performedOn($budget)
                ->causedBy(Auth::user())
                ->withProperties([
                    'filename' => $filename,
                    'path'     => $relativePath,
                ])
                ->log('Generated PDF document for budget #'.$budget->code);

            return $budgetPdf;
        } catch (\Exception $e) {
            Log::error('Error generating budget PDF: '.$e->getMessage(), [
                'budget_id' => $budget->id,
                'trace'     => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Convert a Budget model to a properly formatted state array for PdfGenerator
     *
     * @param Budget $budget
     * @return array
     */
    protected function convertBudgetToState(Budget $budget): array
    {
        $state = $budget->toArray();

        // Ensure content is in the expected format for the PDF template
        if (isset($state['content']) && ! isset($state['content'][0]) && is_array($state['content'])) {
            $state['content'] = [$state['content']];
        }

        // If products are loaded through the pivot relationship, convert them to the expected format
        if ($budget->products && $budget->products->count() > 0 && (! isset($state['content'][0]['products']) || empty($state['content'][0]['products']))) {
            $productItems = [];

            foreach ($budget->products as $product) {
                $productItems[] = [
                    'product'        => $product->id,
                    'product_option' => $product->pivot->product_option_id,
                    'location'       => $product->pivot->location_id,
                    'quantity'       => $product->pivot->quantity,
                    'price'          => $product->pivot->price,
                    'subtotal'       => $product->pivot->subtotal,
                ];
            }

            // Set products in the state
            $state['content'][0]['products'] = $productItems;
        }

        return $state;
    }

    /**
     * Generate a shareable link for the budget PDF
     *
     * @param Budget|int $budget Budget model or ID
     * @param int $expirationMinutes Minutes until URL expires (default: 4320 - 72 hours)
     * @return string|null The shareable link or null if generation failed
     */
    public function generateShareableLink($budget, int $expirationMinutes = 4320): ?string
    {
        try {
            // Get the budget model if an ID was passed
            if (! ($budget instanceof Budget)) {
                $budget = Budget::findOrFail($budget);
            }

            // Generate or get the PDF
            $pdf = $this->generatePdf($budget);
            if (! $pdf) {
                return null;
            }

            // Generate a download URL
            return $pdf->getDownloadUrl($expirationMinutes);
        } catch (\Exception $e) {
            Log::error('Error generating shareable link: '.$e->getMessage(), [
                'budget_id' => $budget->id ?? null,
            ]);

            return null;
        }
    }

    /**
     * Generate a unique filename for the budget PDF
     *
     * @param Budget $budget
     * @return string
     */
    protected function generateFilename(Budget $budget): string
    {
        return Str::slug($budget->code.'-'.$budget->id).'-'.now()->format('YmdHis').'.pdf';
    }
}
