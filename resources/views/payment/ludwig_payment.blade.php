@extends('layouts.depan')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-lg">
        <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="text-center mb-8">
                <img src="/images/ludwig-payment-logo.png" 
                     alt="Ludwig Payment" 
                     class="h-16 mx-auto mb-4"
                     onerror="this.src='/images/payment-default.png'">
                <h1 class="text-2xl font-bold text-gray-900">Konfirmasi Pembayaran</h1>
            </div>

            @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <div class="bg-indigo-50 p-4 rounded-lg mb-6">
                <h2 class="font-medium text-indigo-900 mb-2">Detail Pembayaran:</h2>
                <div class="flex justify-between text-sm text-indigo-800 mb-2">
                    <span>Kode Pembayaran:</span>
                    <span class="font-medium">{{ $payment->payment_id }}</span>
                </div>
                <div class="flex justify-between text-sm text-indigo-800 mb-2">
                    <span>Jumlah:</span>
                    <span class="font-medium">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-indigo-800">
                    <span>Status:</span>
                    <span class="font-medium uppercase">{{ $payment->status }}</span>
                </div>
            </div>

            <form action="{{ route('ludwig.process', $payment->payment_id) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">
                        Masukkan 6 Digit PIN Ludwig Payment
                    </label>
                    <input type="password" 
                           id="pin" 
                           name="pin" 
                           maxlength="6"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="******"
                           required>
                    <p class="mt-2 text-xs text-gray-500">
                        PIN digunakan untuk verifikasi identitas Anda. Jangan bagikan PIN kepada siapapun.
                    </p>
                </div>
                
                <button type="submit"
                        class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg text-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Konfirmasi Pembayaran
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-indigo-600">
                    Kembali ke halaman sebelumnya
                </a>
            </div>

            <div class="mt-8 text-center text-xs text-gray-500">
                <p>© {{ date('Y') }} Ludwig Payment. Semua hak dilindungi.</p>
                <p class="mt-1">Dilindungi dengan enkripsi SSL 256-bit</p>
            </div>
        </div>
    </div>
</div>
@endsection