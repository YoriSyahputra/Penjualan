<head>
    <title>Cetak Resi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', 'Roboto', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .print-controls h1 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #4b0082;
        }
        .print-controls button {
            background: linear-gradient(135deg, #8a2be2, #4b0082);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .print-controls p {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        
        /* Resi compact styling */
        .resi-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .resi-item {
            page-break-after: always;
            width: 100%;
            max-width: 500px; /* Ukuran resi yang compact */
            margin: 0 auto 20px;
        }
        .resi-sticker {
            width: 100%;
            padding: 12px;
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #8a2be2, #4b0082);
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }
        .header::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                45deg,
                rgba(255,255,255,0.1),
                rgba(255,255,255,0.1) 10px,
                transparent 10px,
                transparent 20px
            );
        }
        .header .logo {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header .order-number {
            font-size: 14px;
            margin-top: 5px;
            opacity: 0.9;
        }
        .info-section {
            border: 1px solid #e6e6e6;
            border-radius: 6px;
            margin-bottom: 15px;
            padding: 12px;
            background-color: #fafafa;
        }
        .info-section h2 {
            font-size: 15px;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4b0082;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .info-row strong {
            width: 30%;
            color: #555;
        }
        .info-row span {
            width: 65%;
            font-weight: 500;
        }
        .destination-box {
            border: 2px dashed #8a2be2;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: -2%;
            background-color: #f9f4ff;
            width: 70%;
        }
        .destination-box p {
            margin: 5px 0;
        }
        .destination-box p:first-child {
            font-weight: bold;
            color: #4b0082;
        }
        .item-list {
    border: 1px solid #e6e6e6;
    padding: 12px;
    margin-top: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    background-color: rgb(255, 255, 255);
    width: 70%;
    color: #000; /* Mengubah warna teks menjadi hitam */
}
.item-list h3 {
    font-size: 15px;
    margin-bottom: 8px;
    font-weight: bold;
    color: rgb(0, 0, 0);
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}
.item-list p {
    font-size: 13px;
    margin: 5px 0;
    position: relative;
    padding-left: 15px;
}
.item-list p:before {
    content: "•";
    position: absolute;
    left: 0;
    color: #8a2be2;
}

        
        /* Perbaikan barcode */
        .barcode-section {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }
        .barcode1d {
            width: 100%;
            border: 1px solid #e6e6e6;
            padding: 12px;
            border-radius: 6px;
            background-color: #ffffff;
            text-align: center;
            display: flex;          
            flex-direction: column; 
            align-items: center;    
            justify-content: center; 
            font-family: sans-serif, bold;
        }

        .barcode2d {
            position: absolute;
            top: 5;
            right: 0;
            width: 28%;
            text-align: center;
            padding: 12px;
            border-radius: 6px;
            margin-top:20%;
            display: inline-block;
        }
        .barcode2d img, .barcode1d img {
            max-width: 100%;
            height: auto;
            align-items:center;
        }
        .tracking-number {
            font-size: 10px;
            font-weight: bold;
            color: rgb(0, 0, 0);
            margin-top: 10px;
            text-align: center;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #777;
            margin-top: 12px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        /* Perbaikan print styling */
        @media print {
            @page {
                size: 120mm 180mm; /* Ukuran thermal printer yang lebih tinggi untuk menghindari pemotongan */
                margin: 0;
            }
            
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background: none;
            }
            
            .print-controls {
                display: none;
            }
            
            .resi-container {
                width: 100%;
                padding: 0;
            }
            
            .resi-item {
                page-break-after: always;
                page-break-inside: avoid;
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 5px; /* Menambahkan sedikit padding */
                box-shadow: none;
                height: auto; /* Penting: agar konten tidak terpotong */
                overflow: visible; /* Pastikan overflow tidak terpotong */
                position: relative; /* Untuk memastikan semua konten terlihat */
            }
            
            .resi-sticker {
                width: 100%;
                margin: 0;
                padding: 8px;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                box-shadow: none;
                height: auto; /* Penting: memastikan konten tidak terpotong */
                overflow: visible; /* Pastikan konten tidak terpotong */
            }
            
            /* Mengurangi margin dan padding untuk mencegah pemotongan */
            .item-list, .destination-box {
                margin-bottom: 10px;
            }
            
            /* Mengurangi ukuran font untuk footer */
            .footer {
                margin-top: 8px;
                padding-top: 8px;
                font-size: 10px;
            }
            
            .barcode2d, 
            .barcode2d svg,
            .barcode2d img {
                display: block !important;
                visibility: visible !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
            
            .header, .info-section, .barcode-section, .destination-box, .item-list, .footer {
                page-break-inside: avoid;
            }
        }
        .back-button {
            display: inline-block;
            background-color: #4CAF50; /* Warna latar hijau */
            color: #fff; /* Warna teks putih */
            padding: 10px 20px;
            margin-left: -80%;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #45a049;
        }

    </style>
</head>
<body>
    
    <div class="print-controls">
        <a href="{{ route('dashboard.orders.index') }}" class="back-button">Kembali ke Orders</a>
        <h1>Cetak {{ count($orders ?? []) }} Resi</h1>
        <button onclick="window.print()">Cetak Semua Resi</button>
        <p>Resi akan dicetak dengan ukuran compact</p>
    </div>
    
    <div class="resi-container">
        @foreach($orders as $order)
        <div class="resi-item">
            <div class="resi-sticker">
                <!-- HEADER -->
                <div class="header">
                    <div class="logo">LUDWIG SHIPPING</div>
                    <div class="order-number">Order #{{ $order->order_number }}</div>
                </div>
                
                <!-- INFORMASI PENGIRIMAN & PENERIMA -->
                <div class="info-section">
                    <h2>Data Pengiriman</h2>
                    <div class="info-row">
                        <strong>Customer</strong>
                        <span>{{ $order->user->name }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Kurir</strong>
                        <span>{{ $order->shipping_kurir }}</span>
                    </div>
                </div>
                
                <!-- BARCODE SECTION -->
                <div class="barcode-section">
                    <div class="barcode1d">
                        {!! DNS1D::getBarcodeHTML($order->order_number, 'C128', 1.5, 40) !!}
                        <p>{{$order->order_number }}</p>
                    </div>
                    <div class="barcode2d">
                        {!! DNS2D::getBarcodeHTML($order->nomor_resi, 'QRCODE', 5, 5) !!}
                        <p class="tracking-number">{{ $order->nomor_resi }}</p>    
                    </div>
                </div>
                
                <!-- ALAMAT TUJUAN -->
                <div class="destination-box">
                    <p>TUJUAN PENGIRIMAN:</p>
                    <p>{{ $order->Address->kota }}, {{ $order->Address->provinsi }}</p>
                </div>

                <!-- DAFTAR BARANG -->
                <div class="item-list">
                    <h3>Daftar Barang:</h3>
                    @foreach($order->items as $item)
                        <p>{{ $item->product->name }} ({{ $item->quantity }} pcs)</p>
                    @endforeach
                </div>
                
                <!-- FOOTER -->
                <div class="footer">
                    Ludwig Shipping - Solusi Pengiriman Terpercaya<br>
                    Hubungi: -0882001371542
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <script>
        // Memastikan barcode dan QR code ke-render dengan sempurna
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                console.log('Mengecek barcode elements...');
                var barcodes = document.querySelectorAll('.barcode1d svg, .barcode2d svg');
                barcodes.forEach(function(el) {
                    el.classList.add('print-safe');
                    el.setAttribute('data-print-ready', 'true');
                });
            }, 500);
            
            // Menambahkan event listener untuk memastikan semua konten tercetak
            window.addEventListener('beforeprint', function() {
                document.querySelectorAll('.resi-item').forEach(function(item) {
                    item.style.height = 'auto';
                    item.style.overflow = 'visible';
                });
            });
        });
    </script>
</body>