<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $filters;

    /**
     * ProductExport constructor.
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::query()->report($this->filters)->get(['upc', 'model', 'description']);
    }

    public function headings(): array
    {
        return [
            __('validation.attributes.code'),
            __('validation.attributes.model'),
            __('validation.attributes.description'),
            __('validation.attributes.price'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
