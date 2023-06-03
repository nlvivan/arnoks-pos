<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    protected $from_date;

    protected $to_date;

    public function __construct($from_date, $to_date)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function collection()
    {
        $transactions = Transaction::query()
        ->when($this->from_date && $this->to_date, function ($query) {
            $query->whereBetween('created_at', [Carbon::parse($this->from_date)->startOfDay(), Carbon::parse($this->to_date)->startOfDay()]);
        }, function ($query) {
            $query->whereDate('created_at', Carbon::today());
        })->get();

        return $transactions;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Transaction Number',
            'Cash',
            'Change',
            'Total Amount',
            'Date',
            // Add more headers as needed
        ];
    }
}
