<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfGeneratorService
{
    /**
     * Common options for PDF generation
     */
    protected function getOptions(): array
    {
        return [
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'isPhpEnabled' => true,
        ];
    }

    /**
     * Generate a PDF from a view and download it.
     */
    public function downloadFromView(string $view, array $data = [], string $filename = 'document.pdf', string $paper = 'a4', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paper, $orientation);
        $pdf->setOptions($this->getOptions());
        $pdf->setWarnings(false);

        return $pdf->download($filename);
    }

    /**
     * Generate a PDF from a view and stream it to the browser.
     */
    public function streamFromView(string $view, array $data = [], string $filename = 'document.pdf', string $paper = 'a4', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paper, $orientation);
        $pdf->setOptions($this->getOptions());
        $pdf->setWarnings(false);

        return $pdf->stream($filename);
    }

    /**
     * Generate a PDF from HTML string and download it.
     */
    public function downloadFromHtml(string $html, string $filename = 'document.pdf', string $paper = 'a4', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($paper, $orientation);
        $pdf->setOptions($this->getOptions());
        $pdf->setWarnings(false);

        return $pdf->download($filename);
    }

    /**
     * Generate a PDF from a view and save it to a file.
     */
    public function saveFromView(string $view, array $data, string $path, string $paper = 'a4', string $orientation = 'portrait')
    {
        // Garantir que o diretório existe
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paper, $orientation);
        $pdf->setOptions($this->getOptions());
        $pdf->setWarnings(false);
        $pdf->save($path);

        return $path;
    }
}
