# SIDARLOG — Sistem Informasi Data & Arsip Logistik

<div align="center">
  <img src="public/img/logo-daerah.png" width="100" alt="Logo BPBD Kabupaten Tasikmalaya">
  <br><br>
  <strong>BADAN PENANGGULANGAN BENCANA DAERAH</strong><br>
  <em>Kabupaten Tasikmalaya</em>
  <br><br>
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white">
  <img alt="MySQL" src="https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white">
  <img alt="License" src="https://img.shields.io/badge/Lisensi-Internal%20BPBD-orange?style=flat-square">
</div>

---

**SIDARLOG** adalah platform web manajemen logistik kebencanaan terpadu yang dikembangkan khusus untuk **BPBD Kabupaten Tasikmalaya** dalam rangka Kerja Praktik mahasiswa. Sistem ini dirancang untuk mengelola inventaris perlengkapan/logistik kebencanaan, melacak transaksi barang masuk dan keluar, mencatat mutasi stok antar gudang, melakukan audit fisik (stock opname), serta menghasilkan dokumen **Berita Acara Serah Terima (BAST)** dan laporan logistik resmi secara otomatis dalam format PDF dan Excel.

---

## 🚀 Fitur Utama

- **Dashboard Real-Time** — Statistik stok barang, transaksi terbaru, barang menipis/habis, dan notifikasi sistem.
- **Manajemen Master Data**:
  - **Barang (Inventory)** — Pencatatan barang logistik kebencanaan dengan kode unik, kategori, satuan besar/kecil, harga, sumber anggaran, lokasi gudang, dan QR Code otomatis.
  - **Kategori & Satuan** — Pengelompokan barang (Logistik Makanan, Peralatan SAR, Medis, dll) dan definisi unit ukuran.
  - **Multi-Gudang** — Pengelolaan beberapa gudang (misal: Gudang Logistik Utama, Gudang Peralatan SAR).
  - **Pihak Kesatu & Kedua** — Data instansi/personil yang menyerahkan dan menerima bantuan logistik.
  - **Referensi BAP & Sumber Anggaran** — Nomor Berita Acara Pemeriksaan (misal: `300.2.2/BA.xxx/Darlog/2026`) dan asal anggaran (APBD/APBN/Hibah).
- **Transaksi Logistik**:
  - **Barang Masuk** — Pencatatan penerimaan logistik dari BNPB/pemasok dengan nomor referensi, tanggal, dan gudang.
  - **Barang Keluar** — Pencatatan distribusi bantuan logistik ke kecamatan/desa terdampak bencana.
  - **Cetak Berita Acara Serah Terima (BAST)** — Dokumen resmi PDF berformat surat dinas lengkap dengan KOP surat, logo BPBD, tanda tangan 3 pihak, dan tanggal terbilang.
  - **Mutasi Stok** — Pemindahan logistik antar gudang dengan alur persetujuan (APPROVED/PENDING).
- **Stock Opname** — Audit stok fisik berkala untuk mencocokkan stok sistem dengan kondisi riil di gudang.
- **Pelaporan & Export**:
  - Laporan stok barang, transaksi, stock opname, dan mutasi gudang.
  - Export ke **PDF** (barryvdh/laravel-dompdf) dengan KOP surat resmi dan logo.
  - Export ke **Excel** (maatwebsite/excel) dengan KOP dan logo instansi.
- **Manajemen Pengguna & RBAC**:
  - Manajemen User, Jabatan, Bidang, Role, dan Hak Akses.
  - 6 level pengguna: *Super Admin, Admin Logistik, Staff Gudang, Kepala Bidang, Pimpinan, Operator Portal*.
  - Reset password oleh Super Admin ke password default.
- **Log Aktivitas & Notifikasi** — Pencatatan otomatis semua aksi user dan pusat notifikasi real-time.

---

## 🛠️ Tech Stack

| Lapisan | Teknologi |
|:---|:---|
| **Framework** | [Laravel 12.x](https://laravel.com) |
| **Runtime** | PHP >= 8.2 |
| **Frontend Styling** | Tailwind CSS (via CDN) + Alpine.js |
| **Build Tool** | Vite |
| **Database** | MySQL / MariaDB |
| **Auth & RBAC** | [spatie/laravel-permission](https://github.com/spatie/laravel-permission) |
| **Export PDF** | [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) |
| **Export Excel** | [maatwebsite/excel](https://laravel-excel.com) |
| **QR Code** | [simplesoftwareio/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode) |

---

## ⚙️ Persyaratan Sistem

Pastikan perangkat Anda sudah terpasang:

- PHP >= 8.2 (dengan ekstensi: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `fileinfo`, `xml`)
- [Composer](https://getcomposer.org)
- [Node.js](https://nodejs.org) & NPM
- MySQL / MariaDB (versi 8.0+)

---

## 💻 Panduan Instalasi Lokal

### 1. Clone Repositori

```bash
git clone <repository-url>
cd sidarlog
```

### 2. Instalasi Lengkap via Composer Script

```bash
composer run setup
```

Script ini secara otomatis akan:
1. Menginstal semua package PHP (`composer install`)
2. Menyalin `.env.example` menjadi `.env`
3. Membuat application key (`php artisan key:generate`)
4. Menjalankan migrasi database (`php artisan migrate`)
5. Menginstal package Node.js (`npm install`)
6. Mem-build aset frontend (`npm run build`)

### 3. Konfigurasi Database

Buka file `.env` dan sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sidarlog
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Seed Database (Data Awal & Akun Pengguna)

```bash
php artisan db:seed
```

Atau jika ingin reset total (hapus semua data lama, migrasi ulang, seed):

```bash
php artisan migrate:fresh --seed
```

### 5. Upload Logo Instansi

Letakkan file logo BPBD Kabupaten Tasikmalaya di:

```
public/img/logo-daerah.png
```

Logo ini akan tampil di semua dokumen PDF (BAST & laporan) serta di header Excel.

---

## 🏃 Menjalankan Aplikasi

### Mode Development (Semua Service Sekaligus)

```bash
composer run dev
```

Perintah ini menjalankan secara bersamaan:
- `php artisan serve` — Server web Laravel
- `php artisan queue:listen` — Worker antrian (notifikasi)
- `php artisan pail` — Log watcher real-time
- `npm run dev` — Vite dev server (hot reload aset)

### Mode Manual

```bash
php artisan serve         # Server web → http://127.0.0.1:8000
npm run dev               # Vite dev server (opsional)
php artisan queue:listen  # Worker antrian (untuk notifikasi)
```

---

## 🔐 Akun Akses Default

Semua akun menggunakan password: **`password`**

| NIP | Nama | Role | Hak Akses |
|:---|:---|:---|:---|
| `1234567890` | Administrator Utama | `super_admin` | Akses penuh semua fitur |
| `1111111111` | Andi Logistikawan | `admin_logistik` | Kelola logistik, transaksi & laporan |
| `2222222222` | Budi Gudang | `staff_gudang` | Catat transaksi masuk/keluar & opname |
| `3333333333` | Citra Kabid | `kabid` | Verifikasi mutasi & lihat laporan |
| `4444444444` | Dedi Pimpinan | `pimpinan` | Pantau dashboard & laporan |
| `5555555555` | Eka Operator | `operator_portal` | Hak akses baca saja |

> **Catatan:** Jika ada user yang lupa password, Super Admin dapat mereset ke password default dari menu **Sistem → Manajemen User → Reset Password**. Setelah login, user disarankan segera mengubah password via menu **Profil**.

---

## 📁 Struktur Direktori Penting

```
sidarlog/
├── app/
│   ├── Exports/              ← Kelas export Excel (Items, Transaksi, Opname, Mutasi)
│   ├── Http/Controllers/     ← Controller aplikasi
│   ├── Models/               ← Eloquent Models
│   ├── Observers/            ← Auto-logging aktivitas user
│   └── Services/             ← Business logic (TransactionService)
├── database/
│   ├── migrations/           ← Skema tabel database
│   └── seeders/              ← Data awal & data dummy realistis BPBD
├── public/
│   └── img/                  ← Taruh logo-daerah.png di sini!
├── resources/
│   └── views/
│       ├── layouts/          ← Layout utama (app.blade.php)
│       ├── pages/            ← Halaman-halaman fitur
│       └── reports/          ← Template PDF (bast, items, transactions, opname, mutasi)
└── routes/
    └── web.php               ← Definisi semua route
```

---

## 📄 Informasi Institusi

Dokumen PDF dan laporan resmi yang dihasilkan sistem menggunakan kop surat:

```
PEMERINTAH DAERAH KABUPATEN TASIKMALAYA
BADAN PENANGGULANGAN BENCANA DAERAH
Jl. Otto Iskandardinata No. 19 Tasikmalaya  Telp/Fax (0265) 334111
Email: bpbd@tasikmalayakab.go.id  |  TASIKMALAYA - 46113
```

**Kepala Pelaksana:** RONI, A.Ks., M.M — NIP. 19690901 199303 1 004

---

## 📋 Lisensi & Keterangan

Proyek ini dikembangkan sebagai bagian dari **Kerja Praktik** pada **BPBD Kabupaten Tasikmalaya**. Penggunaan, distribusi, dan modifikasi kode hanya untuk keperluan internal instansi.

---

<div align="center">
  <em>Dikembangkan dengan ❤️ untuk BPBD Kabupaten Tasikmalaya — 2026</em>
</div>
