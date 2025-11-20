<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfGeneratorService
{
    /**
     * Generate a PDF from a view and download it.
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $paper
     * @param string $orientation
     * @return \Illuminate\Http\Response
     */
    public function downloadFromView(string $view, array $data = [], string $filename = 'document.pdf', string $paper = 'a4', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paper, $orientation);
        $pdf->setWarnings(false);

        return $pdf->download($filename);
    }

    /**
     * Generate a PDF from a view and stream it to the browser.
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $paper
     * @param string $orientation
     * @return \Illuminate\Http\Response
     */
    public function streamFromView(string $view, array $data = [], string $filename = 'document.pdf', string $paper = 'a4', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paper, $orientation);
        $pdf->setWarnings(false);

        return $pdf->stream($filename);
    }

    /**
     * Generate a PDF from HTML string and download it.
     *
     * @param string $html
     * @param string $filename
     * @param string $paper
     * @param string $orientation
     * @return \Illuminate\Http\Response
     */
    public function downloadFromHtml(string $html, string $filename = 'document.pdf', string $paper = 'a4', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($paper, $orientation);
        $pdf->setWarnings(false);

        return $pdf->download($filename);
    }

    /**
     * Generate a PDF from a view and save it to a file.
     *
     * @param string $view
     * @param array $data
     * @param string $path
     * @param string $paper
     * @param string $orientation
     * @return string
     */
    public function saveFromView(string $view, array $data, string $path, string $paper = 'a4', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paper, $orientation);
        $pdf->setWarnings(false);
        $pdf->save($path);

        return $path;
    }
}
