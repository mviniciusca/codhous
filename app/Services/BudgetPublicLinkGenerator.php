<?php

namespace App\Services;

use App\Models\Budget;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BudgetPublicLinkGenerator
{
    /**
     * The budget to generate link for
     */
    protected Budget $budget;

    /**
     * The PDF Generator instance
     */
    protected ?PdfGenerator $pdfGenerator = null;

    /**
     * PDF file path if it exists
     */
    protected ?string $pdfPath = null;

    /**
     * Path to the PDF in storage
     */
    protected ?string $storagePath = null;

    /**
     * Link expiration time in minutes
     */
    protected int $expirationMinutes;

    /**
     * Create a new BudgetPublicLinkGenerator instance
     *
     * @param Budget|int $budget Budget instance or ID
     * @param int $expirationMinutes Minutes until link expires (default: 60)
     */
    public function __construct($budget, int $expirationMinutes = 60)
    {
        // Load budget if ID was passed
        if (! ($budget instanceof Budget)) {
            $budget = Budget::findOrFail($budget);
        }

        $this->budget = $budget;
        $this->expirationMinutes = $expirationMinutes;

        // Find existing PDF if it exists
        $this->findExistingPdf();
    }

    /**
     * Find existing PDF for this budget
     *
     * @return bool True if PDF exists
     */
    protected function findExistingPdf(): bool
    {
        // Check if budget has a PDF document associated
        if (! empty($this->budget->pdf_document)) {
            $path = env('PDF_DOWNLOAD_PATH', 'app/public/').$this->budget->pdf_document;

            if (file_exists(storage_path($path))) {
                $this->pdfPath = storage_path($path);
                $this->storagePath = $path;

                return true;
            }
        }

        return false;
    }

    /**
     * Generate a PDF if it doesn't exist
     *
     * @return bool True if PDF was created or already exists
     */
    protected function ensurePdfExists(): bool
    {
        // If PDF already exists, we're good
        if ($this->pdfPath) {
            return true;
        }

        try {
            // Create PDF generator
            $this->pdfGenerator = new PdfGenerator($this->budget->toArray());

            // Generate the PDF
            $this->pdfGenerator->generate();

            // Update paths
            $this->pdfPath = $this->pdfGenerator->filePath;
            $this->storagePath = env('PDF_DOWNLOAD_PATH', 'app/public/').$this->pdfGenerator->generateFilename();

            return true;
        } catch (\Exception $e) {
            Log::error('Error generating PDF for budget link: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Generate public download link for the budget PDF
     *
     * @return string|null The public download URL or null if it fails
     */
    public function generateLink(): ?string
    {
        // Make sure PDF exists
        if (! $this->ensurePdfExists()) {
            return null;
        }

        try {
            // Get the relative storage path
            $relativePath = Str::replaceFirst('app/public/', 'public/', $this->storagePath);

            // Generate a signed temporary URL
            $url = URL::temporarySignedRoute(
                'budget.download',
                now()->addMinutes($this->expirationMinutes),
                ['budget' => $this->budget->id]
            );

            return $url;
        } catch (\Exception $e) {
            Log::error('Error generating public link for budget: '.$e->getMessage());

            return null;
        }
    }
}
