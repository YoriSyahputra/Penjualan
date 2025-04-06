@extends('layouts.driver')

@section('content')
@php
    use App\Constants\DeliveryStatus;
@endphp
<div class="container mx-auto py-4 px-4 sm:px-6">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('driver.dashboard') }}" class="text-blue-700 hover:text-blue-900 transition-colors">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">PROSES PENGIRIMAN</h2>
            </div>
        </div>

        <!-- Success or Error Message -->
        @if (session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            {{ session('error') }}
        </div>
        @endif

        <!-- Order Details Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white py-4 px-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold">Detail Paket</h3>
                    <div class="flex items-center">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-white text-blue-800">
                            @if($order->status_order == 'on_delivery' || $order->status_order == 'processing')
                                Sedang Diantar
                            @else
                                {{ $order->status_order }}
                            @endif
                        </span>
                        
                        @if($delivery && $delivery->status == 'on_the_way' || $delivery && in_array($delivery->status, ['sedang_diantar', 'menuju_alamat', 'tiba_di_tujuan']))
                        <!-- Status Dropdown untuk Driver -->
                        <form action="{{ route('driver.update.delivery.status', $order->id) }}" method="POST" class="ml-2">
                            @csrf
                            <input type="hidden" name="lat" id="status_lat" value="{{ $delivery->location_lat ?? '' }}">
                            <input type="hidden" name="lng" id="status_lng" value="{{ $delivery->location_lng ?? '' }}">
                            <select name="status" onchange="this.form.submit()" class="text-xs rounded border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="{{ \App\Constants\DeliveryStatus::SEDANG_DIANTAR }}" {{ $delivery->status == \App\Constants\DeliveryStatus::SEDANG_DIANTAR ? 'selected' : '' }}>Sedang Diantar</option>
                                <option value="{{ \App\Constants\DeliveryStatus::MENUJU_ALAMAT }}" {{ $delivery->status == \App\Constants\DeliveryStatus::MENUJU_ALAMAT ? 'selected' : '' }}>Menuju Alamat</option>
                                <option value="{{ \App\Constants\DeliveryStatus::TIBA_DI_TUJUAN }}" {{ $delivery->status == \App\Constants\DeliveryStatus::TIBA_DI_TUJUAN ? 'selected' : '' }}>Tiba di Tujuan</option>
                            </select>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nomor Order</p>
                        <p class="font-semibold">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nomor Resi</p>
                        <p class="font-semibold">{{ $order->nomor_resi }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Penerima</p>
                        <p class="font-semibold">{{ $order->nama_penerima ?? $order->user->name }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Alamat Lengkap</p>
                        <div class="flex flex-col space-y-2">
                            <div class="relative">
                                <p id="alamat_text" class="font-semibold">{{ $order->alamat_lengkap }}</p>
                                <p class="font-semibold">{{ $order->kecamatan }}, {{ $order->kota }}</p>
                                <p class="font-semibold">{{ $order->provinsi }} {{ $order->kode_pos }}</p>
                            </div>
                            
                            <!-- Tombol Aksi Maps yang Baru -->
                            <div class="flex space-x-2 mt-2">
                                <button type="button" onclick="copyAlamat()"  class="flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                    Salin Alamat
                                </button>
                                
                                <a href="https://www.google.com/maps" target="_blank" class="flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    Buka Maps
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Telepon</p>
                        <p class="font-semibold">{{ $order->no_hp ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-gray-100 py-3 px-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Aksi Pengiriman</h3>
            </div>
            <div class="p-6 space-y-4">
            @if (!$delivery || $delivery->status == \App\Constants\DeliveryStatus::PICKED_UP)
                    @if (!$delivery)
                    <!-- Jika belum diambil -->
                    <form action="{{ route('driver.start.delivery', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg text-sm font-medium flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Ambil Paket
                        </button>
                    </form>
                    @else
                    <!-- Jika sudah diambil, belum dikirim -->
                    <form action="{{ route('driver.update.delivery.status', $order->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="sedang_diantar">
                        <input type="hidden" name="lat" id="current_lat">
                        <input type="hidden" name="lng" id="current_lng">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg text-sm font-medium flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Mulai Pengiriman
                        </button>
                    </form>
                    @endif
                    @elseif($delivery->status == \App\Constants\DeliveryStatus::SEDANG_DIANTAR || $delivery->status == \App\Constants\DeliveryStatus::MENUJU_ALAMAT)
                    <!-- Tampilkan pesan menunggu status "tiba di tujuan" -->
                    <div class="text-center py-4">
                        <p class="text-gray-500">Silakan ubah status pengiriman ke "Tiba di Tujuan" saat sudah sampai di lokasi tujuan untuk melanjutkan proses pengiriman.</p>
                    </div>
                    @elseif($delivery->status == \App\Constants\DeliveryStatus::TIBA_DI_TUJUAN)
                    <!-- Form selesaikan pengiriman hanya muncul jika status "tiba di tujuan" -->
                    <form action="{{ route('driver.complete.delivery', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="lat" id="delivery_lat">
                        <input type="hidden" name="lng" id="delivery_lng">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                                Status Pengiriman
                            </label>
                            <select id="status" name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="{{ \App\Constants\DeliveryStatus::TIBA_DI_TUJUAN }}">Paket Terkirim</option>
                                <option value="{{ \App\Constants\DeliveryStatus::GAGAL }}">Gagal Terkirim</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                                Catatan
                            </label>
                            <textarea id="notes" name="notes" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3" placeholder="Contoh: Diterima oleh Bapak Joko"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="photo_proof">
                                Bukti Foto <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col w-full h-32 border-2 border-blue-200 border-dashed hover:bg-gray-100 hover:border-blue-300 rounded-lg">
                                    <div id="preview_container" class="flex flex-col items-center justify-center pt-7 hidden">
                                        <img id="preview_image" src="#" alt="Preview" class="h-20 w-auto object-cover">
                                        <p class="pt-1 text-sm tracking-wider text-gray-600 group-hover:text-gray-600">Ubah Foto</p>
                                    </div>
                                    <div id="placeholder" class="flex flex-col items-center justify-center pt-7">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 group-hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="pt-1 text-sm tracking-wider text-gray-400 group-hover:text-gray-600">Ambil Foto Bukti</p>
                                    </div>
                                    <input type="file" name="photo_proof" id="photo_proof" class="opacity-0" accept="image/*" capture="camera" required />
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg text-sm font-medium flex items-center justify-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Selesaikan Pengiriman
                        </button>
                    </form>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">Pengiriman sudah selesai</p>
                        <a href="{{ route('driver.dashboard') }}" class="mt-3 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Kembali ke Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="popup" class="popup hidden">
  <div class="popup-content">
    <div class="checkmark-container">
      <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
        <circle class="checkmark-circle" cx="26" cy="26" r="24" fill="none"/>
        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
      </svg>
    </div>
    <p>Alamat berhasil disalin</p>
  </div>
</div>

</div>
<style>
.popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }

  .popup.show {
    opacity: 1;
    pointer-events: auto;
  }

  .popup-content {
    background: #fff;
    padding: 30px 40px; /* tambahkan padding agar animasi tidak terpotong */
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    overflow: visible; /* pastikan tidak memotong animasi */
  }

  .checkmark-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px;
    overflow: visible; /* jika masih terpotong, tambah ini juga */
  }

  .checkmark {
    width: 60px;
    height: 60px;
    stroke: green;
    stroke-width: 3;
    /* pastikan transform-origin di tengah */
    transform-origin: center;
  }

  /* Animasi Lingkaran */
  .checkmark-circle {
    stroke: green;
    stroke-width: 3;
    stroke-dasharray: 151;
    stroke-dashoffset: 150;
    animation: drawCircle 0.6s ease-out forwards, bounceIn 0.5s ease-out;
    transform-origin: center; /* atur origin di tengah */
  }

  /* Animasi Ceklis */
  .checkmark-check {
    stroke: green;
    stroke-width: 3;
    stroke-dasharray: 30;
    stroke-dashoffset: 30;
    animation: drawCheck 0.4s ease-out 0.4s forwards;
    transform-origin: center; /* atur origin di tengah */
  }

  @keyframes drawCircle {
    to {
      stroke-dashoffset: 0;
    }
  }

  @keyframes drawCheck {
    to {
      stroke-dashoffset: 0;
    }
  }

  @keyframes bounceIn {
    0% {
      transform: scale(0);
      opacity: 0;
    }
    60% {
      transform: scale(1.1);
      opacity: 1;
    }
    100% {
      transform: scale(1);
    }
  }

  .hidden { display: none; }
</style>


<!-- Script section -->

<script>
    // Preview foto saat diupload
    const photoInput = document.getElementById('photo_proof');
    const previewImage = document.getElementById('preview_image');
    const previewContainer = document.getElementById('preview_container');
    const placeholder = document.getElementById('placeholder');
    
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Fungsi untuk salin alamat
    function copyAlamat() {
        // Ambil alamat lengkap dari data yang ada
        const alamatLengkap = "{{ $order->alamat_lengkap }}, {{ $order->kecamatan }}, {{ $order->kota }}, {{ $order->provinsi }} {{ $order->kode_pos }}";

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(alamatLengkap)
                .then(() => {
                    console.log("Alamat berhasil disalin.");
                    showPopup();
                })
                .catch((err) => {
                    console.error("Gagal menyalin:", err);
                    // Jika terjadi error, coba fallback
                    fallbackCopy(alamatLengkap);
                });
        } else {
            fallbackCopy(alamatLengkap);
        }
    }

    function fallbackCopy(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.top = "-9999px";
        document.body.appendChild(textArea);
        textArea.select();

        try {
            document.execCommand('copy');
            console.log("Fallback: Alamat berhasil disalin.");
            showPopup();
        } catch (err) {
            console.error("Fallback: Gagal menyalin:", err);
        }
        document.body.removeChild(textArea);
    }

    function showPopup() {
        const popup = document.getElementById('popup');
        popup.classList.remove('hidden');
        popup.classList.add('show');

        // Sembunyikan pop-up setelah 3 detik
        setTimeout(() => {
            popup.classList.remove('show');
            popup.classList.add('hidden');
        }, 1500);
    }
</script>

@endsection