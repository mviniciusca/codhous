<?php

namespace App\Exports;

use App\Models\Budget;
use App\Models\Location;
use App\Models\ProductOption;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BudgetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithColumnWidths, WithStyles
{
    protected $budget;

    public function __construct(Budget $budget)
    {
        $this->budget = $budget;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->budget->products);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('Product'),
            __('Option'),
            __('Location'),
            __('Quantity (m³)'),
            __('Unit Price'),
            __('Subtotal'),
        ];
    }

    /**
     * @param $product
     * @return array
     */
    public function map($product): array
    {
        $option = ProductOption::find($product->pivot->product_option_id);
        $location = Location::find($product->pivot->location_id);

        return [
            $product->name,
            $option?->name ?? '-',
            $location?->name ?? '-',
            $product->pivot->quantity,
            number_format($product->pivot->price, 2, ',', '.'),
            number_format($product->pivot->subtotal, 2, ',', '.'),
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Budget '.$this->budget->code;
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 25,
            'C' => 25,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
