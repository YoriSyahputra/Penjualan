<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Pengiriman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            margin-bottom: 30px;
        }
        .logo {
            max-height: 50px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        .status-delivered {
            color: green;
            font-weight: bold;
        }
        .status-failed {
            color: red;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-picked_up {
            color: blue;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Riwayat Pengiriman Paket</h1>
        <p>Tanggal Export: {{ date('d/m/Y H:i') }}</p>
        <p>Driver: {{ Auth::user()->name }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Resi</th>
                <th>Kurir</th>
                <th>Status</th>
                <th>Alamat</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $index => $history)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $history->created_at->format('d M Y, H:i') }}</td>
                    <td>{{ $history->order->nomor_resi ?? 'N/A' }}</td>
                    <td>{{ $history->order->shipping_kurir ?? 'N/A' }}</td>
                    <td class="status-{{ $history->status }}">
                        {{ ucwords(str_replace('_', ' ', $history->status)) }}
                    </td>
                    <td>
                        @if($history->order)
                            {{ $history->order->alamat_lengkap . ', ' . 
                               $history->order->kecamatan . ', ' . 
                               $history->order->kota }}
                        @else
                            Alamat tidak tersedia
                        @endif
                    </td>
                    <td>{{ $history->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data riwayat pengiriman</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis pada {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>