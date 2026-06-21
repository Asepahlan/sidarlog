# SIDARLOG — Sistem Informasi Data & Arsip Logistik

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

**SIDARLOG** adalah platform web manajemen logistik terpadu yang dirancang untuk mengelola inventaris, melacak transaksi barang masuk dan keluar, mencatat mutasi stok antar gudang, melakukan audit fisik (stock opname), serta menghasilkan dokumen Berita Acara Serah Terima (BAST) dan laporan logistik secara otomatis dalam format PDF dan Excel.

Aplikasi ini dikembangkan menggunakan framework **Laravel 12**, styled dengan **Tailwind CSS v4** melalui Vite, serta dilengkapi sistem otorisasi Role & Permission menggunakan **Spatie Laravel Permission**.

---

## 🚀 Fitur Utama

- **Dashboard Real-Time**: Informasi statistik tingkat tinggi mengenai stok barang, mutasi, transaksi terbaru, serta optimasi sistem cache.
- **Manajemen Master Data**:
  - **Barang (Inventory)**: Pencatatan barang dengan kode unik, kategori, satuan, deskripsi, dan QR Code otomatis (SVG). Dilengkapi fitur Trash (Soft Deletes), Restore, dan Force Delete.
  - **Kategori & Satuan**: Pengelompokan barang dan definisi unit ukuran (misalnya: Pcs, Rim, Box).
  - **Gudang & Lokasi**: Pengelolaan multi-gudang (Warehouse) dan detail rak penyimpanan barang.
  - **Pihak Kesatu & Kedua**: Informasi instansi atau personil yang menyerahkan dan menerima barang.
  - **Berita Acara Penyerahan (BAP) & Sumber Anggaran**: Dokumen referensi hukum transaksi dan asal dana barang.
- **Transaksi Logistik**:
  - **Barang Masuk**: Pencatatan stok masuk beserta nomor referensi, asal anggaran, dan gudang tujuan.
  - **Barang Keluar**: Pencatatan stok keluar beserta pencetakan Berita Acara Serah Terima (BAST).
  - **Mutasi Stok**: Pemindahan barang antar gudang dengan alur persetujuan (Approval) pihak berwenang.
- **Stock Opname**: Audit stok fisik secara berkala untuk mencocokkan stok sistem dengan stok riil di gudang.
- **Pelaporan & Export**:
  - Laporan stok barang, transaksi masuk/keluar, stock opname, dan mutasi gudang.
  - Export data secara dinamis ke file **PDF** (via DomPDF) dan **Excel** (via Maatwebsite Excel).
- **Manajemen Pengguna & Otorisasi**:
  - Manajemen User, Jabatan, Bidang, serta Role.
  - Role-based Access Control (RBAC) dengan 6 level pengguna bawaan: *Super Admin, Admin Logistik, Staff Gudang, Kepala Bidang (Kabid), Pimpinan, dan Operator Portal*.
- **Log Aktivitas & Notifikasi**:
  - Pencatatan otomatis log audit aktivitas user (seperti pembuatan, pengeditan, atau penghapusan data) menggunakan model Observers.
  - Pusat notifikasi real-time untuk user.

---

## 🛠️ Tech Stack & Dependencies

- **Framework**: [Laravel 12.x](https://laravel.com)
- **Runtime**: PHP >= 8.2
- **Styling**: [Tailwind CSS v4](https://tailwindcss.com) (via `@tailwindcss/vite`)
- **Build Tool**: [Vite](https://vite.dev)
- **Database**: MySQL / SQLite
- **Dependencies Utama (Composer)**:
  - `spatie/laravel-permission` — Mengatur hak akses & role.
  - `barryvdh/laravel-dompdf` — Export laporan ke format PDF.
  - `maatwebsite/excel` — Export laporan ke format Excel.
  - `simplesoftwareio/simple-qrcode` — Membuat QR Code barang secara dinamis.

---

## ⚙️ Persyaratan Sistem

Pastikan perangkat Anda sudah terpasang:
- PHP >= 8.2 (dengan ekstensi `pdo`, `mbstring`, `openssl`, `gd`, `zip`)
- Composer
- Node.js & NPM
- Database server (MySQL/MariaDB)

---

## 💻 Panduan Instalasi Lokal

### 1. Klon Repositori
```bash
git clone <repository-url>
cd sidarlog
```

### 2. Konfigurasi Lingkungan (Environment) & Setup
Aplikasi menyediakan command setup instan via Composer untuk menginstal semua dependency dan mempersiapkan aplikasi:
```bash
composer run setup
```
*Script setup di atas akan melakukan:*
- Pemasangan package Composer (`composer install`).
- Penyalinan `.env.example` menjadi `.env` (jika belum ada).
- Pembuatan Application Key (`php artisan key:generate`).
- Migrasi database (`php artisan migrate --force`).
- Instalasi package Node (`npm install`).
- Build aset Frontend (`npm run build`).

### 3. Konfigurasi Database
Buka file `.env` yang baru dibuat di root project dan sesuaikan konfigurasi koneksi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sidarlog
DB_USERNAME=username_anda
DB_PASSWORD=password_anda
```

### 4. Seed Database (Data Awal & Pengguna Test)
Jalankan seeder untuk mengisi data master awal, izin akses (permissions), dan akun-akun uji coba:
```bash
php artisan db:seed
```

---

## 🔐 Akun Akses Uji Coba

Gunakan kredensial berikut untuk masuk ke sistem setelah menjalankan perintah seeder (semua akun menggunakan password: `password`):

| NIP | Nama Pengguna | Role | Email | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| **1234567890** | Super Admin | `super_admin` | `admin@sidarlog.test` | Akses penuh sistem (bypass all gates) |
| **1111111111** | Logistik Admin | `admin_logistik` | `andi@sidarlog.test` | Mengelola data logistik, transaksi, & laporan |
| **2222222222** | Staff Gudang | `staff_gudang` | `budi@sidarlog.test` | Mencatat transaksi masuk/keluar & opname stok |
| **3333333333** | Kabid Logistik | `kabid` | `citra@sidarlog.test` | Melakukan verifikasi/approve mutasi & melihat laporan |
| **4444444444** | Pimpinan | `pimpinan` | `dedi@sidarlog.test` | Memantau dashboard & melihat laporan |
| **5555555555** | Operator Portal | `operator_portal` | `eka@sidarlog.test` | Hak akses baca saja (viewer) |

---

## 🏃 Menjalankan Aplikasi

Aplikasi menyediakan script running concurrently yang menjalankan server lokal Laravel, antrean queue (untuk notifikasi/email), log watcher, dan Vite dev server secara bersamaan:

```bash
composer run dev
```

Atau jika ingin menjalankannya secara terpisah:
- **Server Web**: `php artisan serve`
- **Vite Dev Server (Assets)**: `npm run dev`
- **Queue Listener**: `php artisan queue:listen`
