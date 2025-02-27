@extends('layouts.depan')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-lg">
        <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-green-100">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Berhasil!</h1>
                <p class="text-gray-600">Terima kasih, pembayaran Anda telah kami terima.</p>
            </div>

            <div class="bg-green-50 p-4 rounded-lg mb-6">
                <h2 class="font-medium text-green-900 mb-2">Detail Transaksi:</h2>
                <div class="flex justify-between text-sm text-green-800 mb-2">
                    <span>Kode Pembayaran:</span>
                    <span class="font-medium">{{ $payment->payment_id }}</span>
                </div>
                <div class="flex justify-between text-sm text-green-800 mb-2">
                    <span>Referensi Transaksi:</span>
                    <span class="font-medium">{{ $payment->transaction_reference }}</span>
                </div>
                <div class="flex justify-between text-sm text-green-800 mb-2">
                    <span>Jumlah:</span>
                    <span class="font-medium">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-green-800">
                    <span>Waktu Pembayaran:</span>
                    <span class="font-medium">{{ $payment->paid_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div class="mb-6">
                <a href="{{ route('home') }}" 
                   class="block w-full bg-indigo-600 text-white py-3 px-6 rounded-lg text-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-center">
                    Kembali ke Beranda
                </a>
            </div>

            <div class="text-center">
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800" onclick="window.print()">
                    <span class="flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak Bukti Pembayaran
                    </span>
                </a>
            </div>

            <div class="mt-8 text-center text-xs text-gray-500">
                <p>© {{ date('Y') }} Ludwig Payment. Semua hak dilindungi.</p>
                <p class="mt-1">Simpan bukti pembayaran ini sebagai referensi</p>
            </div>
        </div>
    </div>
</div>
@endsection