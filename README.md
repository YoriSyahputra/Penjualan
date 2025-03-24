<!-- Logo Section -->
<div class="flex justify-center">
    <div class="animate-bounce">
        <svg class="w-12 h-12 sm:w-16 sm:h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
    </div>
</div>

# LudWIg

LudWig adalah aplikasi ECommerce berbasis web yang dibangun menggunakan **Laravel 10 MIX**. Aplikasi ini menyediakan pengalaman belanja online yang mudah, cepat, dan aman bagi pengguna, serta dilengkapi dengan berbagai fitur unggulan untuk admin.

## Fitur

### Untuk Pengguna (USER)
- **Order Barang**  
  Pengguna dapat memesan barang dengan mudah sesuai kebutuhan.
- **Keranjang Belanja**  
  Tambahkan barang favorit ke dalam keranjang untuk pembelian yang lebih terencana.
- **Monitoring Status Pesanan**  
  Cek status pesanan seperti *paid*, *pending*, dan *cancel* dengan cepat.
- **Transfer Uang Antar Pengguna**  
  Fitur aman untuk mentransfer uang ke sesama pengguna.
- **Pembayaran dengan Kode**  
  Bayar pesanan melalui kode pembayaran setelah melakukan place order.

### Untuk Administrator (ADMIN)
- **Manajemen Produk dan Kategori**  
  Kelola data produk, stok, dan kategori untuk memastikan ketersediaan barang.
- **Pengelolaan Transaksi**  
  Monitor dan validasi semua transaksi pengguna dengan sistem yang terintegrasi.
- **Kontrol Hak Akses**  
  Atur peran dan hak akses untuk memastikan keamanan dan efisiensi operasional.
- **Laporan & Statistik**  
  Dapatkan insight mendalam tentang performa penjualan melalui laporan terperinci.

## Instalasi

Ikuti langkah-langkah berikut untuk menginstall dan menjalankan aplikasi:

1. **Clone Repository**
   ```bash
   git clone 'https://github.com/YoriSyahputra/Penjualan.git'
2. **Install Package PHP**
    ```bash
    composer install
3. **Install dependencies front-end**
    ```bash
    npm install
4. **Migrasi Database**
    ```bash
    php artisan migrate
5. **Seed Data**
    ```bash
    php artisan db:seed --class=CategoriesTableSeeder
6. **Builds Assets**
    ```bash
    npm run build
7. **Jalankan Server**
    ```bash
    php artisan serve

# Web Sudah Bisa Digunakan #