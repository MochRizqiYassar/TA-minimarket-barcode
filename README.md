Arthapura – Sistem Informasi Inventori dan Penjualan Toko

Arthapura merupakan aplikasi berbasis web yang dikembangkan sebagai Tugas Akhir Program Studi [Nama Program Studi Anda], [Nama Institusi Anda].

Sistem ini dibangun untuk membantu proses pengelolaan stok barang, transaksi kulakan (pembelian ke supplier), penjualan (kasir), hingga pelaporan pada sebuah toko, dengan menerapkan metode pengembangan Rapid Application Development (RAD) agar sistem dapat dibangun secara cepat, iteratif, dan mudah disesuaikan dengan kebutuhan pengguna di lapangan.

🚀 Fitur Utama

- Login dengan sistem role (Admin & Kasir) beserta approval akun oleh Admin
- Manajemen data barang, kategori, dan tipe barang
- Manajemen data supplier
- Kulakan (pembelian stok ke supplier) beserta approval kulakan
- Scan nota kulakan otomatis menggunakan OCR (Tesseract)
- Barang masuk (penerimaan stok)
- Transaksi penjualan (kasir) beserta cetak struk
- Generate barcode barang (otomatis maupun manual)
- Monitoring stok real-time & notifikasi stok minimum
- Laporan barang masuk, laporan penjualan, dan laporan barang terlaris (export PDF)
- Dashboard Admin dan Dashboard Kasir
- Dukungan mode offline (Service Worker + sinkronisasi data) untuk transaksi kasir

🛠️ Teknologi yang Digunakan

Backend
- Laravel 13
- PHP 8.3
- MySQL / SQLite

Frontend
- Blade
- Livewire
- Tailwind CSS
- Alpine.js
- Vite

Pendukung Lainnya
- barryvdh/laravel-dompdf — export laporan & struk ke PDF
- milon/barcode — generate barcode barang
- thiagoalessio/tesseract_ocr — pembacaan teks otomatis dari nota kulakan
- intervention/image — pengolahan gambar
- Service Worker & IndexedDB — dukungan mode offline

⚙️ Instalasi

📋 Prasyarat

Pastikan telah menginstal:

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- MySQL (atau cukup SQLite untuk pengembangan lokal)
- Tesseract OCR (untuk fitur scan nota kulakan)
- Git

1. Clone Repository

```bash
git clone git clone https://github.com/MochRizqiYassar/TA2026-362258302088-MochRizqiYassar.git
cd TA2026-362258302088-MochRizqiYassar
```

2. Install Dependency

```bash
composer install
npm install
```

3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database pada file `.env`. Secara default project ini sudah dikonfigurasi menggunakan SQLite (`DB_CONNECTION=sqlite`); apabila ingin menggunakan MySQL, ubah bagian `DB_*` sesuai kebutuhan.

4. Konfigurasi Tesseract OCR

Tambahkan path instalasi Tesseract pada file `.env`:

```
TESSERACT_PATH="C:/Program Files/Tesseract-OCR/tesseract.exe"
TESSDATA_PREFIX="C:/Program Files/Tesseract-OCR/tessdata"
```

Sesuaikan path di atas dengan lokasi instalasi Tesseract OCR pada perangkat Anda.

5. Konfigurasi Identitas Toko

Tambahkan pada file `.env` untuk menampilkan identitas toko pada struk penjualan:

```
TOKO_NAMA="Arthapura"
TOKO_ALAMAT="Banyuwangi"
TOKO_TELEPON="081515522904"
```

6. Migrasi dan Seeder

```bash
php artisan migrate --seed
```

Atau, jika akun admin dibuat menggunakan seeder terpisah:

```bash
php artisan db:seed --class=AdminSeeder
```

7. Menjalankan Aplikasi

Jalankan Vite:

```bash
npm run dev
```

Buka terminal baru, kemudian jalankan server Laravel:

```bash
php artisan serve
```

Secara default aplikasi dapat diakses melalui:

```
http://127.0.0.1:8000
```

> Alternatif: gunakan `composer run dev` untuk menjalankan server, queue listener, log viewer, dan Vite secara bersamaan dalam satu perintah.

🧩 Metode Pengembangan: Rapid Application Development (RAD)

Sistem ini dikembangkan menggunakan metode **Rapid Application Development (RAD)**, yaitu model pengembangan perangkat lunak yang menekankan pada siklus pengembangan singkat, iteratif, dan melibatkan pengguna secara aktif dalam setiap tahapannya. Tahapan RAD yang diterapkan pada pengembangan sistem ini meliputi:

1. Requirements Planning
   Identifikasi kebutuhan fungsional dan non-fungsional sistem melalui wawancara/observasi terhadap proses bisnis toko, seperti alur kulakan, penjualan, dan pelaporan stok.

2. User Design
   Perancangan alur sistem, struktur basis data, serta antarmuka (UI/UX) dilakukan secara iteratif bersama calon pengguna (admin dan kasir), sehingga rancangan dapat langsung disesuaikan berdasarkan umpan balik.

3. Construction
   Tahap membangun sistem sekaligus melakukan pengujian pada setiap modul yang telah selesai dikembangkan (barang, kulakan, penjualan, laporan, dsb.), sehingga perbaikan dapat dilakukan secara cepat tanpa menunggu seluruh sistem selesai.

4. Cutover (Implementation)
   Tahap pengujian akhir secara menyeluruh, pelatihan pengguna, serta implementasi sistem untuk digunakan secara nyata pada proses operasional toko.

Pendekatan RAD dipilih karena kebutuhan sistem bersifat dinamis dan memerlukan keterlibatan pengguna secara langsung untuk menghasilkan sistem yang sesuai dengan proses bisnis toko yang sebenarnya.

📈 Evaluasi Sistem

Kinerja sistem dievaluasi menggunakan:

- **Black Box Testing**, untuk memastikan setiap fungsi sistem (manajemen barang, kulakan, penjualan, laporan, dll.) berjalan sesuai dengan yang diharapkan.
- **User Acceptance Testing (UAT)**, untuk mengukur tingkat penerimaan pengguna (admin dan kasir) terhadap sistem berdasarkan aspek fungsionalitas dan kemudahan penggunaan.

👤 Pengembang

| Informasi     | Detail                            |
|---------------|-----------------------------------|
| Nama          | Moch.Rizqi Yassar                 |
| NIM           | 362258302088                      |
| Program Studi | Teknologi Rekayasa Perangkat Lunak|
| Institusi     | Politeknik Negeri Banyuwangi      |
| Email         | kikioryassar.2003@gmail.com       |
