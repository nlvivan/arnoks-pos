<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    public function collection()
    {
        return Product::query()
        ->get(['id', 'name', 'quantity']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Product',
            'Stock',
            // Add more headers as needed
        ];
    }
}
