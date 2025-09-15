<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class BarcodeHistoryExport implements FromCollection, WithHeadings
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        return new Collection($this->data);
    }
    public function headings(): array
    {
        return ['Thời gian', 'Barcode 1', 'Barcode 2', 'Số lượng', 'Kết quả'];
    }
}
