<?php

namespace App\Exports;

use App\Models\DeliveryHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Str;

class DeliveryHistoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $histories;

    public function __construct($histories)
    {
        $this->histories = $histories;
    }

    public function collection()
    {
        return $this->histories;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nomor Resi',
            'Kurir',
            'Status',
            'Alamat',
            'Catatan',
        ];
    }

    public function map($history): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $alamat = '';
        if ($history->order) {
            $alamat = $history->order->alamat_lengkap . ', ' . 
                   $history->order->kecamatan . ', ' . 
                   $history->order->kota;
        } else {
            $alamat = 'Alamat tidak tersedia';
        }

        $status = ucwords(str_replace('_', ' ', $history->status));
        
        return [
            $rowNumber,
            $history->created_at->format('d M Y, H:i'),
            $history->order->nomor_resi ?? 'N/A',
            $history->order->shipping_kurir ?? 'N/A',
            $status,
            $alamat,
            $history->notes,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}