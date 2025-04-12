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
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;

class YearlySalesExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting, WithTitle
{
    // Definisikan konstanta format IDR
    const FORMAT_CURRENCY_IDR = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';
    
    protected $storeId;
    protected $year;
    protected $storeName;
    protected $totalItems = 0;
    protected $totalRevenue = 0;

    public function __construct(int $storeId, string $year, ?string $storeName = null)
    {
        $this->storeId = $storeId;
        $this->year = $year;
        
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
        $salesData = collect();
        $i = 1;

        // Ambil data penjualan untuk tahun yang dipilih
        $orders = Order::where('store_id', $this->storeId)
            ->where('status_order', '!=', 'cancelled')
            ->whereYear('paid_at', $this->year)
            ->get();

        // Kelompokkan data per bulan
        $monthlySummary = collect();
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::createFromDate($this->year, $month, 1)->format('F');
            $monthlyOrders = $orders->filter(function($order) use ($month) {
                return Carbon::parse($order->paid_at)->month == $month;
            });
            
            $monthlyQuantity = 0;
            $monthlyRevenue = 0;
            
            foreach ($monthlyOrders as $order) {
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->store_id == $this->storeId) {
                        $monthlyQuantity += $item->quantity;
                        $monthlyRevenue += ($item->price * $item->quantity);
                    }
                }
            }
            
            $this->totalItems += $monthlyQuantity;
            $this->totalRevenue += $monthlyRevenue;
            
            $salesData->push([
                'no' => $i++,
                'month' => $monthName,
                'quantity' => $monthlyQuantity,
                'revenue' => $monthlyRevenue,
            ]);
        }

        return $salesData;
    }

    public function headings(): array
    {
        return [
            'No',
            'Bulan',
            'Jumlah Terjual',
            'Total Pendapatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);

        // Judul laporan
        $sheet->insertNewRowBefore(1, 4);
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN TAHUNAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A2', 'Tahun: ' . $this->year);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A3:D3');
        $sheet->setCellValue('A3', $this->storeName);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

        // Header style
        $sheet->getStyle('A5:D5')->getFont()->setBold(true);
        $sheet->getStyle('A5:D5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:D5')->getFill()->setFillType('solid')->getStartColor()->setRGB('E9EFFB');

        // Tambahkan summary di bawah data
        $lastRow = $sheet->getHighestRow() + 2;
        $sheet->setCellValue('A' . $lastRow, 'TOTAL TAHUNAN:');
        $sheet->getStyle('A' . $lastRow)->getFont()->setBold(true);

        $sheet->setCellValue('B' . $lastRow, '');
        $sheet->setCellValue('C' . $lastRow, $this->totalItems);
        $sheet->setCellValue('D' . $lastRow, $this->totalRevenue);
        $sheet->getStyle('D' . $lastRow)->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_IDR);
        $sheet->getStyle('A' . $lastRow . ':D' . $lastRow)->getFont()->setBold(true);

        // Add chart
        $this->addSalesChart($sheet);

        return [
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9EFFB']]],
        ];
    }

    protected function addSalesChart(Worksheet $sheet)
    {
        $dataSeriesLabels = [
            new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$C$5', null, 1), // Jumlah Terjual
        ];
        
        $xAxisTickValues = [
            new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Worksheet!$B$6:$B$17', null, 12), // Bulan
        ];
        
        $dataSeriesValues = [
            new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Worksheet!$C$6:$C$17', null, 12),
        ];
        
        // Build the dataseries
        $series = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART,
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $series->setPlotDirection(\PhpOffice\PhpSpreadsheet\Chart\DataSeries::DIRECTION_COL);
        
        // Set the series in the plot area
        $plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$series]);
        
        // Set the chart legend
        $legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(
            \PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT,
            null,
            false
        );
        
        $title = new \PhpOffice\PhpSpreadsheet\Chart\Title('Penjualan Bulanan ' . $this->year);
        $yAxisLabel = new \PhpOffice\PhpSpreadsheet\Chart\Title('Jumlah Terjual');
        
        // Create the chart
        $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
            'chart1',
            $title,
            $legend,
            $plotArea,
            true,
            0,
            null,
            $yAxisLabel
        );
        
        // Set the position where the chart should appear
        $chart->setTopLeftPosition('A' . ($sheet->getHighestRow() + 5));
        $chart->setBottomRightPosition('D' . ($sheet->getHighestRow() + 20));
        
        // Add the chart to the worksheet
        $sheet->addChart($chart);
    }

    public function columnFormats(): array
    {
        return [
            'D' => self::FORMAT_CURRENCY_IDR,
        ];
    }

    public function title(): string
    {
        return $this->storeName . ' - ' . $this->year;
    }
}