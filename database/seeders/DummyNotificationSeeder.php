<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BudgetSource;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\StockMutation;
use App\Models\StockOpname;
use App\Models\StockTransaction;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\FirstParty;
use App\Models\SecondParty;
use App\Models\ReferenceBap;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $now = Carbon::now();

        // ── CATEGORIES ──────────────────────────────────────────────
        $cats = [];
        $categoriesList = [
            'Logistik Makanan',
            'Sandang & Perlengkapan Tidur',
            'Medis & Higienitas',
            'Huntara & Hunian',
            'Peralatan Evakuasi & SAR'
        ];
        foreach ($categoriesList as $n) {
            $cats[$n] = Category::firstOrCreate(['nama_kategori' => $n]);
        }

        // ── UNITS ───────────────────────────────────────────────────
        $units = [];
        $unitsList = [
            ['Pcs', 'pcs'],
            ['Dus', 'dus'],
            ['Box', 'box'],
            ['Lembar', 'lbr'],
            ['Unit', 'unit'],
            ['Paket', 'pkt'],
            ['Pack', 'pck'],
            ['Botol', 'btl']
        ];
        foreach ($unitsList as [$n, $s]) {
            $units[$n] = Unit::firstOrCreate(['nama_satuan' => $n], ['simbol' => $s]);
        }

        // ── BUDGET SOURCES ──────────────────────────────────────────
        $sources = [];
        $sourcesList = [
            ['APBD Kab. Tasikmalaya', '2026'],
            ['APBD Prov. Jawa Barat', '2026'],
            ['APBN (Dana Siap Pakai BNPB)', '2026'],
            ['Hibah Masyarakat / CSR', '2026']
        ];
        foreach ($sourcesList as [$n, $y]) {
            $sources[$n] = BudgetSource::firstOrCreate(['nama_sumber' => $n], ['tahun_anggaran' => $y]);
        }

        // ── ITEM LOCATIONS ──────────────────────────────────────────
        $locs = [];
        $locationsList = [
            'Rak A (Bahan Makanan)',
            'Rak B (Peralatan Tidur)',
            'Gudang Peralatan SAR',
            'Rak Medis & Sanitasi',
            'Posko Bencana Utama'
        ];
        foreach ($locationsList as $n) {
            $locs[$n] = ItemLocation::firstOrCreate(['nama_lokasi' => $n]);
        }

        // ── WAREHOUSES ──────────────────────────────────────────────
        $wh1 = Warehouse::firstOrCreate(['kode_gudang' => 'GD-LOG-UTAMA'], ['nama_gudang' => 'Gudang Logistik Utama', 'lokasi' => 'Lantai 1']);
        $wh2 = Warehouse::firstOrCreate(['kode_gudang' => 'GD-SAR-EVAKUASI'], ['nama_gudang' => 'Gudang Peralatan SAR & Evakuasi', 'lokasi' => 'Samping Posko']);
        $wh3 = Warehouse::firstOrCreate(['kode_gudang' => 'GD-LOG-CADANGAN'], ['nama_gudang' => 'Gudang Cadangan Singaparna', 'lokasi' => 'Kec. Singaparna']);

        // ── FIRST PARTIES ────────────────────────────────────────────
        $fp1 = FirstParty::firstOrCreate(
            ['nip' => '196909011993031004'],
            ['nama_pihak' => 'RONI, A.Ks., M.M', 'jabatan' => 'Kepala Pelaksana BPBD', 'instansi' => 'BPBD Kabupaten Tasikmalaya']
        );
        $fp2 = FirstParty::firstOrCreate(
            ['nip' => '197805122005011002'],
            ['nama_pihak' => 'Asep Sunandar', 'jabatan' => 'Kabid Kedaruratan dan Logistik', 'instansi' => 'BPBD Kabupaten Tasikmalaya']
        );
        $fp3 = FirstParty::firstOrCreate(
            ['nip' => '198504022010121001'],
            ['nama_pihak' => 'Herry Setiawan', 'jabatan' => 'Staf Logistik Operasional', 'instansi' => 'BPBD Kabupaten Tasikmalaya']
        );

        // ── SECOND PARTIES ───────────────────────────────────────────
        $sp1 = SecondParty::firstOrCreate(
            ['nip' => '197508122003121002'],
            ['nama_pihak' => 'Drs. Heri', 'jabatan' => 'Camat Singaparna', 'instansi' => 'Kecamatan Singaparna']
        );
        $sp2 = SecondParty::firstOrCreate(
            ['nip' => '198205142009042001'],
            ['nama_pihak' => 'Siti Aminah', 'jabatan' => 'Kepala Desa Cipatujah', 'instansi' => 'Desa Cipatujah, Kecamatan Cipatujah']
        );
        $sp3 = SecondParty::firstOrCreate(
            ['nama_pihak' => 'H. Ujang'],
            ['nip' => '-', 'jabatan' => 'Ketua Posko Pengungsi Darurat', 'instansi' => 'Desa Karangnunggal, Kecamatan Karangnunggal']
        );

        // ── REFERENCE BAPs ───────────────────────────────────────────
        $bap1 = ReferenceBap::firstOrCreate(
            ['nomor_ba' => '300.2.2/BA.012/Darlog/2026'],
            ['judul_ba' => 'Serah Terima Logistik Korban Banjir Cipatujah', 'tgl_ba' => '2026-06-15', 'keterangan' => 'Penyerahan sembako dan sandang ke desa terdampak']
        );
        $bap2 = ReferenceBap::firstOrCreate(
            ['nomor_ba' => '300.2.2/BA.015/Darlog/2026'],
            ['judul_ba' => 'Serah Terima Peralatan SAR Evakuasi Longsor Singaparna', 'tgl_ba' => '2026-06-18', 'keterangan' => 'Penyerahan chainsaw dan rompi pelampung untuk relawan kecamatan']
        );
        $bap3 = ReferenceBap::firstOrCreate(
            ['nomor_ba' => '300.2.2/BA.018/Darlog/2026'],
            ['judul_ba' => 'Distribusi Selimut dan Tenda Posko Karangnunggal', 'tgl_ba' => '2026-06-20', 'keterangan' => 'Penyaluran perlengkapan hunian darurat ke posko pengungsian']
        );

        // ── BARANG BPBD REALISTIS ────────────────────────────────────
        // Format: [nama, kategori, satuan_kecil, satuan_besar, harga_kecil, harga_besar, stok_min, stok_saat_ini_kecil, stok_saat_ini_besar, lokasi, sumber, tgl_exp]
        $barangs = [
            ['Mie Instan', 'Logistik Makanan', 'Pcs', 'Dus', 3000, 120000, 50, 480, 12, 'Rak A (Bahan Makanan)', 'APBD Kab. Tasikmalaya', null],
            ['Makanan Siap Saji', 'Logistik Makanan', 'Pcs', 'Dus', 15000, 150000, 20, 150, 15, 'Rak A (Bahan Makanan)', 'APBN (Dana Siap Pakai BNPB)', $now->copy()->addMonths(6)->toDateString()],
            ['Air Mineral 600ml', 'Logistik Makanan', 'Pcs', 'Dus', 2500, 45000, 50, 720, 30, 'Rak A (Bahan Makanan)', 'APBD Kab. Tasikmalaya', $now->copy()->addMonths(12)->toDateString()],
            ['Susu Bayi 400gr', 'Logistik Makanan', 'Box', null, 85000, null, 15, 60, 0, 'Rak A (Bahan Makanan)', 'APBD Prov. Jawa Barat', $now->copy()->addMonths(4)->toDateString()],
            ['Selimut Wol Tebal', 'Sandang & Perlengkapan Tidur', 'Lembar', null, 65000, null, 50, 250, 0, 'Rak B (Peralatan Tidur)', 'APBD Kab. Tasikmalaya', null],
            ['Kain Sarung Dewasa', 'Sandang & Perlengkapan Tidur', 'Pcs', null, 50000, null, 50, 180, 0, 'Rak B (Peralatan Tidur)', 'APBD Kab. Tasikmalaya', null],
            ['Matras Karet Lipat', 'Sandang & Perlengkapan Tidur', 'Pcs', null, 75000, null, 30, 110, 0, 'Rak B (Peralatan Tidur)', 'APBN (Dana Siap Pakai BNPB)', null],
            ['Tenda Pengungsi Besar 4x6', 'Huntara & Hunian', 'Unit', null, 8500000, null, 2, 8, 0, 'Gudang Peralatan SAR', 'APBN (Dana Siap Pakai BNPB)', null],
            ['Tenda Keluarga (Dom)', 'Huntara & Hunian', 'Unit', null, 2500000, null, 5, 20, 0, 'Gudang Peralatan SAR', 'APBD Prov. Jawa Barat', null],
            ['Paket Sembako Darurat', 'Huntara & Hunian', 'Paket', null, 2500000, null, 20, 85, 0, 'Rak A (Bahan Makanan)', 'APBD Kab. Tasikmalaya', null],
            ['Masker Medis 3-Ply', 'Medis & Higienitas', 'Box', null, 40000, null, 10, 120, 0, 'Rak Medis & Sanitasi', 'APBD Kab. Tasikmalaya', $now->copy()->addMonths(24)->toDateString()],
            ['Cairan Hand Sanitizer 500ml', 'Medis & Higienitas', 'Botol', null, 35000, null, 10, 35, 0, 'Rak Medis & Sanitasi', 'APBD Prov. Jawa Barat', $now->copy()->addMonths(12)->toDateString()],
            ['Pembalut Wanita', 'Medis & Higienitas', 'Pack', null, 15000, null, 20, 95, 0, 'Rak Medis & Sanitasi', 'APBD Kab. Tasikmalaya', null],
            ['Popok Bayi (Diapers)', 'Medis & Higienitas', 'Pack', null, 60000, null, 20, 75, 0, 'Rak Medis & Sanitasi', 'APBD Kab. Tasikmalaya', null],
            ['Perahu Karet 6 Penumpang', 'Peralatan Evakuasi & SAR', 'Unit', null, 25000000, null, 1, 4, 0, 'Gudang Peralatan SAR', 'APBN (Dana Siap Pakai BNPB)', null],
            ['Rompi Pelampung (Life Jacket)', 'Peralatan Evakuasi & SAR', 'Pcs', null, 180000, null, 10, 50, 0, 'Gudang Peralatan SAR', 'APBN (Dana Siap Pakai BNPB)', null],
            ['Genset Portable 3000W', 'Peralatan Evakuasi & SAR', 'Unit', null, 6500000, null, 2, 6, 0, 'Gudang Peralatan SAR', 'APBD Prov. Jawa Barat', null],
            ['Gergaji Mesin (Chainsaw)', 'Peralatan Evakuasi & SAR', 'Unit', null, 4200000, null, 2, 5, 0, 'Gudang Peralatan SAR', 'APBD Kab. Tasikmalaya', null],
            ['Sekop Lipat Baja', 'Peralatan Evakuasi & SAR', 'Pcs', null, 95000, null, 10, 24, 0, 'Gudang Peralatan SAR', 'APBD Kab. Tasikmalaya', null],
            ['Cangkul Gagang Kayu', 'Peralatan Evakuasi & SAR', 'Pcs', null, 85000, null, 10, 20, 0, 'Gudang Peralatan SAR', 'APBD Kab. Tasikmalaya', null],
        ];

        $itemObjs = [];
        foreach ($barangs as $i => $b) {
            [$nama, $katNama, $satKecil, $satBesar, $hargaKecil, $hargaBesar, $min, $stokKecil, $stokBesar, $lokNama, $srcNama, $exp] = $b;
            $kode = 'BRG-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $item = Item::firstOrCreate(
                ['kode_barang' => $kode],
                [
                    'nama_barang'        => $nama,
                    'kategori_id'        => $cats[$katNama]->id,
                    'satuan_kecil_id'    => $units[$satKecil]->id,
                    'satuan_besar_id'    => $satBesar ? $units[$satBesar]->id : null,
                    'harga_satuan_kecil' => $hargaKecil,
                    'harga_satuan_besar' => $hargaBesar,
                    'sumber_anggaran_id' => $sources[$srcNama]->id,
                    'lokasi_barang_id'   => $locs[$lokNama]->id,
                    'stok_minimal'       => $min,
                    'stok_saat_ini_kecil'=> $stokKecil,
                    'stok_saat_ini_besar'=> $stokBesar,
                    'tgl_kadaluarsa'     => $exp,
                    'tgl_diterima'       => $now->copy()->subMonths(rand(1,4))->toDateString(),
                    'foto'               => null,
                ]
            );
            $itemObjs[] = $item;
        }

        // ── TRANSAKSI MASUK ──────────────────────────────────────────
        // Format: [barang, gudang, kecil, besar, tgl]
        $txMasuk = [
            [$itemObjs[0],  $wh1, 800,  20, $now->copy()->subDays(25)], // Mie Instan
            [$itemObjs[1],  $wh1, 200,  20, $now->copy()->subDays(24)], // Makanan Siap Saji
            [$itemObjs[2],  $wh1, 960,  40, $now->copy()->subDays(22)], // Air Mineral
            [$itemObjs[4],  $wh1, 300,   0, $now->copy()->subDays(20)], // Selimut
            [$itemObjs[7],  $wh2,  10,   0, $now->copy()->subDays(18)], // Tenda Pengungsi Besar
            [$itemObjs[8],  $wh2,  25,   0, $now->copy()->subDays(17)], // Tenda Keluarga
            [$itemObjs[9],  $wh1, 100,   0, $now->copy()->subDays(15)], // Paket Sembako
            [$itemObjs[14], $wh2,   5,   0, $now->copy()->subDays(12)], // Perahu Karet
            [$itemObjs[15], $wh2,  60,   0, $now->copy()->subDays(10)], // Rompi Pelampung
            [$itemObjs[16], $wh2,   8,   0, $now->copy()->subDays(8)],  // Genset
        ];
        foreach ($txMasuk as $idx => [$item, $wh, $kecil, $besar, $tgl]) {
            StockTransaction::firstOrCreate(
                ['no_referensi' => 'MSK-LOG-' . str_pad($idx+1, 3, '0', STR_PAD_LEFT)],
                [
                    'barang_id'          => $item->id,
                    'gudang_id'          => $wh->id,
                    'pengguna_id'        => $user->id,
                    'jenis'              => 'masuk',
                    'jumlah_barang_kecil'=> $kecil,
                    'jumlah_barang_besar'=> $besar,
                    'penerima_penyerah'  => 'Pemasok Sembako/Peralatan BNPB',
                    'keterangan'         => 'Pengadaan logistik kebencanaan resmi',
                    'tgl_transaksi'      => $tgl,
                ]
            );
        }

        // ── TRANSAKSI KELUAR ─────────────────────────────────────────
        // Format: [barang, gudang, kecil, besar, BAP, penerima, tgl]
        $txKeluar = [
            [$itemObjs[0],  $wh1, 200,  5, $bap2, $sp2, $now->copy()->subDays(10)], // Mie Instan ke Cipatujah
            [$itemObjs[1],  $wh1,  50,  5, $bap2, $sp2, $now->copy()->subDays(10)], // Siap Saji ke Cipatujah
            [$itemObjs[2],  $wh1, 240, 10, $bap2, $sp2, $now->copy()->subDays(10)], // Air Mineral ke Cipatujah
            [$itemObjs[4],  $wh1,  50,  0, $bap3, $sp3, $now->copy()->subDays(8)],  // Selimut ke Karangnunggal
            [$itemObjs[8],  $wh2,   5,  0, $bap3, $sp3, $now->copy()->subDays(8)],  // Tenda Keluarga ke Karangnunggal
            [$itemObjs[9],  $wh1,  15,  0, $bap2, $sp2, $now->copy()->subDays(6)],  // Paket Sembako ke Cipatujah
            [$itemObjs[14], $wh2,   1,  0, $bap1, $sp1, $now->copy()->subDays(5)],  // Perahu Karet ke Singaparna
            [$itemObjs[15], $wh2,  10,  0, $bap1, $sp1, $now->copy()->subDays(5)],  // Rompi Pelampung ke Singaparna
            [$itemObjs[16], $wh2,   2,  0, $bap1, $sp1, $now->copy()->subDays(4)],  // Genset ke Singaparna
            [$itemObjs[17], $wh2,   1,  0, $bap1, $sp1, $now->copy()->subDays(4)],  // Chainsaw ke Singaparna
        ];
        foreach ($txKeluar as $idx => [$item, $wh, $kecil, $besar, $bap, $sp, $tgl]) {
            $fp = ($idx % 2 === 0) ? $fp1 : $fp2;
            StockTransaction::firstOrCreate(
                ['no_referensi' => 'KLR-LOG-' . str_pad($idx+1, 3, '0', STR_PAD_LEFT)],
                [
                    'barang_id'          => $item->id,
                    'gudang_id'          => $wh->id,
                    'pengguna_id'        => $user->id,
                    'pihak_kesatu_id'    => $fp->id,
                    'pihak_kedua_id'     => $sp->id,
                    'reference_bap_id'   => $bap->id,
                    'penerima_penyerah'  => $sp->nama_pihak,
                    'jenis'              => 'keluar',
                    'jumlah_barang_kecil'=> $kecil,
                    'jumlah_barang_besar'=> $besar,
                    'keterangan'         => 'Penyaluran logistik bencana alam daerah',
                    'tgl_transaksi'      => $tgl,
                ]
            );
        }

        // ── MUTASI GUDANG ────────────────────────────────────────────
        $mutasis = [
            [$itemObjs[0],  $wh1, $wh3, 100, $now->copy()->subDays(9)], // Mutasi Mie Instan ke Singaparna
            [$itemObjs[2],  $wh1, $wh3, 200, $now->copy()->subDays(8)], // Mutasi Air Mineral ke Singaparna
            [$itemObjs[4],  $wh1, $wh3,  50, $now->copy()->subDays(6)], // Mutasi Selimut ke Singaparna
            [$itemObjs[15], $wh2, $wh3,  10, $now->copy()->subDays(5)], // Mutasi Life Jacket ke Singaparna
        ];
        foreach ($mutasis as $idx => [$item, $from, $to, $jml, $tgl]) {
            StockMutation::firstOrCreate(
                ['no_mutasi' => 'MUT-LOG-' . str_pad($idx+1, 3, '0', STR_PAD_LEFT)],
                [
                    'barang_id'           => $item->id,
                    'gudang_asal_id'      => $from->id,
                    'gudang_tujuan_id'    => $to->id,
                    'pengguna_id'         => $user->id,
                    'jumlah_barang_kecil' => $jml,
                    'keterangan'          => 'Penyebaran stok logistik wilayah Tasikmalaya Barat',
                    'status'              => 'APPROVED',
                    'tgl_mutasi'          => $tgl,
                ]
            );
        }

        // ── STOCK OPNAME ─────────────────────────────────────────────
        $opnames = [
            [$itemObjs[0],  $wh1, 480, 480,  0], // Mie Instan
            [$itemObjs[1],  $wh1, 150, 148, -2], // Siap Saji
            [$itemObjs[2],  $wh1, 720, 725,  5], // Air Mineral
            [$itemObjs[4],  $wh1, 250, 250,  0], // Selimut Wol
            [$itemObjs[7],  $wh2,   8,   8,  0], // Tenda Pengungsi
        ];
        foreach ($opnames as [$item, $wh, $sistem, $fisik, $selisih]) {
            StockOpname::firstOrCreate(
                ['barang_id' => $item->id, 'gudang_id' => $wh->id, 'stok_sistem' => $sistem],
                [
                    'pengguna_id' => $user->id,
                    'stok_fisik'  => $fisik,
                    'selisih'     => $selisih,
                    'keterangan'  => $selisih == 0 ? 'Pemeriksaan rutin: Stok Sesuai' : ($selisih > 0 ? 'Audit Stok: Selisih Lebih' : 'Audit Stok: Selisih Kurang (Rusak)'),
                ]
            );
        }

        // ── ACTIVITY LOG ─────────────────────────────────────────────
        $logs = [
            ['Login ke sistem SIDARLOG',                      'Autentikasi'],
            ['Inisialisasi Master Data Logistik BPBD',        'Sistem'],
            ['Penerimaan Logistik Baru dari DSP BNPB',        'Inventory'],
            ['Cetak Berita Acara Serah Terima Cipatujah',    'Laporan'],
            ['Verifikasi Mutasi Logistik Tasikmalaya Barat',  'Inventory'],
            ['Melakukan Stock Opname Rutin Triwulan',         'Inventory'],
            ['Mengekspor laporan persediaan barang ke PDF',   'Laporan'],
        ];
        foreach ($logs as [$act, $mod]) {
            ActivityLog::create([
                'pengguna_id' => $user->id,
                'activity'    => $act,
                'module'      => $mod,
                'ip_address'  => '127.0.0.1',
                'user_agent'  => 'Mozilla/5.0 (SIDARLOG BPBD Seeder)',
            ]);
        }

        $this->command->info('✓ DummyNotificationSeeder (BPBD Realistis) selesai.');
    }
}
