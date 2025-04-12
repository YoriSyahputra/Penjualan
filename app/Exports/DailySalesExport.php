<?php

namespace App\Exports;

use App\Models\OrderItem;
use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;

class DailySalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $storeId;
    protected $days;

    public function __construct($storeId, $days = 7)
    {
        $this->storeId = $storeId;
        $this->days = $days;
    }

    public function collection()
    {
        // Ambil data harian dari completed_at
        $dailyData = OrderItem::whereHas('product', function($query) {
                $query->where('store_id', $this->storeId);
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status_order', '!=', 'cancelled')
            ->whereDate('orders.paid_at', '>=', Carbon::now()->subDays($this->days))
            ->select(
                'products.name as product_name',
                'products.sku as sku',
                'order_items.quantity',
                'order_items.price',
                DB::raw('order_items.quantity * order_items.price as subtotal'),
                'orders.paid_at',
                'orders.order_number'
            )
            ->orderBy('orders.paid_at', 'desc')
            ->get();

        return $dailyData;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Produk',
            'SKU',
            'Jumlah Terjual',
            'Harga Satuan',
            'Total Harga',
            'Nomor Order'
        ];
    }

    public function map($row): array
    {
        return [
            Carbon::parse($row->paid_at)->format('d/m/Y H:i'),
            $row->product_name,
            $row->sku ?? 'N/A',
            $row->quantity,
            'Rp ' . number_format($row->price),
            'Rp ' . number_format($row->subtotal),
            $row->order_number
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9EFFB']]],
            'A' => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Daily Sales - Last ' . $this->days . ' Days';
    }
}