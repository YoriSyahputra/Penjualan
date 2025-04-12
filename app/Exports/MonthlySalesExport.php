<?php

namespace App\Exports;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;

class MonthlySalesExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting, WithTitle
{
    // Definisikan konstanta format IDR
    const FORMAT_CURRENCY_IDR = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';
    
    protected $storeId;
    protected $yearMonth;
    protected $storeName;
    protected $totalItems = 0;
    protected $totalRevenue = 0;

    public function __construct(int $storeId, string $yearMonth, ?string $storeName = null)
    {
        $this->storeId = $storeId;
        $this->yearMonth = $yearMonth;
        
        // Jika store name tidak diberikan, ambil dari database
        if ($storeName === null) {
            $store = \App\Models\Store::find($storeId);
            $this->storeName = $store ? $store->store_name ?? 'Toko' : 'Toko';
        } else {
            $this->storeName = $storeName;
        }
    }

    public function collection()
    {
        list($year, $month) = explode('-', $this->yearMonth);
        
        $orders = Order::where('store_id', $this->storeId)
                      ->where('status_order', '!=', 'cancelled')
                      ->whereYear('paid_at', $year)
                      ->whereMonth('paid_at', $month)
                      ->get();

        $salesData = collect();
        $i = 1;

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                // Pastikan item ini milik produk dari toko yang sesuai
                if ($item->product && $item->product->store_id == $this->storeId) {
                    $salesData->push([
                        'no' => $i++,
                        'order_id' => $order->order_number,
                        'date' => Carbon::parse($order->paid_at)->format('d-m-Y'),
                        'product' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->price * $item->quantity,
                    ]);

                    $this->totalItems += $item->quantity;
                    $this->totalRevenue += ($item->price * $item->quantity);
                }
            }
        }

        return $salesData;
    }

    public function headings(): array
    {
        return [
            'No',
            'Order ID',
            'Tanggal',
            'Produk',
            'Jumlah',
            'Harga Satuan',
            'Total'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Judul laporan
        $sheet->insertNewRowBefore(1, 4);
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN BULANAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Periode: ' . Carbon::createFromFormat('Y-m', $this->yearMonth)->format('F Y'));
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', $this->storeName);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

        // Header style
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:G5')->getFill()->setFillType('solid')->getStartColor()->setRGB('E9EFFB');

        // Tambahkan summary di bawah data
        $lastRow = $sheet->getHighestRow() + 2;
        $sheet->setCellValue('A' . $lastRow, 'TOTAL PENJUALAN:');
        $sheet->getStyle('A' . $lastRow)->getFont()->setBold(true);

        $sheet->setCellValue('B' . $lastRow, $this->totalItems . ' items');
        $sheet->setCellValue('F' . $lastRow, 'Total Revenue:');
        $sheet->setCellValue('G' . $lastRow, $this->totalRevenue);
        $sheet->getStyle('G' . $lastRow)->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_IDR);
        $sheet->getStyle('F' . $lastRow . ':G' . $lastRow)->getFont()->setBold(true);

        return [
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9EFFB']]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => self::FORMAT_CURRENCY_IDR,
            'G' => self::FORMAT_CURRENCY_IDR,
        ];
    }

    public function title(): string
    {
        return $this->storeName . ' - ' . Carbon::createFromFormat('Y-m', $this->yearMonth)->format('F Y');
    }
}