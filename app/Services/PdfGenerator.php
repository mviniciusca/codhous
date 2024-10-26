<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Product;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;


class PdfGenerator
{
    /**
     * Pdf Generator 
     */

    public string $format;
    public string $downloadPath;
    public ?string $filePath = null;


    public function __construct(private ?array $state)
    {
        $this->state = $state;
        $this->downloadPath = rtrim(env('PDF_DOWNLOAD_PATH', 'app/public/'), '/') . '/';
        $this->format = 'a4';
    }


    /**
     * Summary of generateFilename
     * @return string
     */
    public function generateFilename(): string
    {
        return Str::slug($this->state['code']
            . $this->state['id'])
            . now()->format('YmdHis')
            . (env('PDF_TERMINATION', '.pdf'));
    }


    /**
     * Summary of generate
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generate()
    {
        $this->filePath = $this->savePath();

        Pdf::view('pdf.invoice', [
            'state' => $this->state,
            'product_name' => $this->getProductName(),
        ])
            ->format($this->format)
            ->save($this->filePath);

        $persist = Budget::query()
            ->where('id', '=', $this->state['id'])
            ->update([
                'pdf_document' => $this->generateFilename(),
            ]);

        if (!$persist) {
            return Notification::make()
                ->title(__('Error on save the document on database.'))
                ->warning()
                ->send();
        }
        Notification::make()
            ->title(__('Document generated with success!'))
            ->success()
            ->send();
        return $this->downloadPdf();
    }


    public function getProductName()
    {
        return Product::select('name')
            ->where('id', '=', $this->state['content']['product'])
            ->first();
    }

    /**
     * Summary of savePath
     * @return string
     */
    public function savePath(): string
    {
        return storage_path(env('PDF_DOWNLOAD_PATH') . $this->generateFilename());
    }


    public function downloadPdf()
    {
        if ($this->filePath && file_exists($this->filePath)) {
            return response()->download($this->filePath);
        }
    }
}
