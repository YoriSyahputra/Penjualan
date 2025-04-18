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

### Untuk Costumer (USER)
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
- **TOPUP Saldo Ludwig**  
  Pengguna dapat melakukan pengisian saldo Ludwig dengan sistem payment code.
- **Store**  
  Pengguna dapat membuat store di dalam halaman Profile dengan sistem pending approve oleh Super Admin.
### Untuk Seller (ADMIN)
- **Manajemen Produk dan Kategori**  
  Kelola data produk, stok, dan kategori untuk memastikan ketersediaan barang.
- **Pengelolaan Transaksi**  
  Monitor dan validasi semua transaksi pengguna dengan sistem yang terintegrasi.
- **Kontrol Hak Akses**  
  Atur peran dan hak akses untuk memastikan keamanan dan efisiensi operasional.
- **Laporan & Statistik**  
  Dapatkan insight mendalam tentang performa penjualan melalui laporan terperinci.
- **List Costumers Kontak**  
  Lihat daftar pelanggan beserta total barang yang telah dibeli oleh masing-masing pengguna. Admin juga dapat memulai percakapan langsung melalui tombol WhatsApp untuk komunikasi yang lebih cepat dan efisien.

### Untuk Super Admin
- **Admin Management**  
  Mengelola persetujuan pendaftaran admin (seller) baru dengan sistem approval.
- **Top Up Requests**  
  Memantau dan mengelola daftar permintaan top up dari pengguna.
- **Manual Top Up**  
  Mengkonfirmasi top up saldo pengguna melalui sistem payment code.
- **Refund History**  
  Melihat dan mengelola riwayat refund dari semua order.
- **Order History**  
  Akses ke riwayat orders dari seluruh store dalam platform.
- **Driver History**  
  Memantau wallet driver dan total pengiriman yang telah dilakukan.
- **Seller History**  
  Mengawasi data seller termasuk jumlah produk dan saldo wallet.

### Untuk Driver
- **Pengiriman Paket**  
  Melakukan pengiriman paket berdasarkan nomor resi yang tersedia dalam sistem.
- **History Pengiriman**  
  Melihat riwayat pengiriman paket beserta bukti foto pengiriman yang telah dilakukan.
- **Detail Pelanggan**  
  Akses informasi pelanggan seperti order number dan nomor telepon, dilengkapi dengan tombol WhatsApp untuk komunikasi langsung dengan pelanggan.

## Teknologi dan Package

### Core Packages
- **Laravel v10.48** - Framework PHP utama
- **Laravel Breeze v1.29** - Scaffolding autentikasi minimal
- **Laravel Sanctum v3.3** - API authentication system

### Database & Storage
- **Laravel Flysystem v3.29** - File storage abstraction
- **Laravel Excel v3.1** - Export/import Excel files menggunakan PhpSpreadsheet
- **PHPSpreadsheet v1.29** - Library untuk manipulasi file spreadsheet

### UI & Frontend
- **Intervention/Image v3.10** - Library manipulasi gambar
- **DomPDF v3.1** - Konversi HTML ke PDF
- **CSS-to-Inline-Styles v2.3** - Konversi CSS ke inline styles

### Utilitas
- **Faker v1.24** - Generator data palsu untuk testing
- **Carbon v2.72** - Manipulasi tanggal dan waktu
- **Guzzle v7.9** - HTTP client library
- **Barcode v12.0** - Generator barcode dan QR code

### Development & Testing
- **PHPUnit v10.5** - Framework testing
- **Laravel Pint v1.19** - Code style fixer
- **Laravel Ignition v2.9** - Error page handler
- **Laravel Tinker v2.10** - REPL untuk Laravel

## Instalasi

Ikuti langkah-langkah berikut untuk menginstall dan menjalankan aplikasi:

1. **Clone Repository**
  ```bash
  git clone https://github.com/YoriSyahputra/Penjualan.git
  ```
2. **Install Package PHP**
  ```bash
  composer install
  ```
3. **Install dependencies front-end**
  ```bash
  npm install
  ```
4. **Seed Data**
  ```bash
  php artisan db:seed --class=CategoriesTableSeeder
  ```
5. **Setup ENV**
  Ubah nama file dari `.env.example` menjadi `.env`, 
  lalu sesuaikan `DB_DATABASE=` dengan nama database yang kamu inginkan.
  ```
6. **Application Key**
  ```bash
  php artisan key:generate
  ```
7. **Migrasi Data**
  ```bash
  php artisan migrate
  ```
8. **Buat Folder Path di Public Storage**
  Jalankan perintah berikut di terminal untuk membuat folder yang diperlukan:
  ```bash
  mkdir -p public/storage/{attachment,comments,componen,delivery_proofs,photo_proofs,produk,profile-photos,store-logos}
  ```
9. **Link Storage**
  ```bash
  php artisan storage:link
  ```
10. **Build Assets**
   ```bash
   npm run build
   ```
11. **Jalankan Server**
   ```bash
   php artisan serve
   ```

12. **Auto Confirm Orders (Opsional)**
    Jika ingin menjalankan fitur konfirmasi order otomatis, jalankan perintah:
    ```bash
    php artisan queue:work
    ```

# Web Sudah Bisa Digunakan #