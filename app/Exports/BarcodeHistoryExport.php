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
        // Đảm bảo xuất đúng thứ tự và tên cột
        return collect($this->data)->map(function($item) {
            return [
                // Nếu là model, lấy thuộc tính, nếu là array thì lấy key
                isset($item->created_at) ? $item->created_at : ($item['created_at'] ?? ''),
                isset($item->barcode1) ? $item->barcode1 : ($item['barcode1'] ?? ''),
                isset($item->barcode2) ? $item->barcode2 : ($item['barcode2'] ?? ''),
                isset($item->quantity) ? $item->quantity : ($item['quantity'] ?? ''),
                isset($item->result) ? $item->result : ($item['result'] ?? ''),
            ];
        });
    }
    public function headings(): array
    {
        return ['Thời gian', 'Barcode 1', 'Barcode 2', 'Số lượng', 'Kết quả'];
    }
}
