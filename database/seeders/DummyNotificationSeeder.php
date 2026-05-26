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
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        // ── MASTER DATA ─────────────────────────────────────────────
        $cats = [];
        foreach (['Alat Tulis Kantor','Elektronik','Peralatan Kebersihan','Perlengkapan Rapat','Arsip'] as $n) {
            $cats[$n] = Category::firstOrCreate(['nama_kategori' => $n]);
        }

        $units = [];
        foreach ([['Pcs','pcs'],['Box','box'],['Rim','rim'],['Unit','unit'],['Pack','pack']] as [$n,$s]) {
            $units[$n] = Unit::firstOrCreate(['nama_satuan' => $n], ['simbol' => $s]);
        }

        $sources = [];
        foreach ([['APBD 2026','2026'],['BOS 2026','2026'],['Hibah','2026']] as [$n,$y]) {
            $sources[$n] = BudgetSource::firstOrCreate(['nama_sumber' => $n], ['tahun_anggaran' => $y]);
        }

        $locs = [];
        foreach (['Gudang Utama','Gudang ATK','Ruang Admin','Ruang Kepala'] as $n) {
            $locs[$n] = ItemLocation::firstOrCreate(['nama_lokasi' => $n]);
        }

        $wh1 = Warehouse::firstOrCreate(['kode_gudang' => 'GD-001'], ['nama_gudang' => 'Gudang Pusat',    'lokasi' => 'Lantai 1']);
        $wh2 = Warehouse::firstOrCreate(['kode_gudang' => 'GD-002'], ['nama_gudang' => 'Gudang Cadangan', 'lokasi' => 'Lantai 2']);

        // ── BARANG DUMMY ────────────────────────────────────────────
        $now   = Carbon::now();
        $barangs = [
            // [nama, kategori, satuan, harga, stok_min, stok_saat_ini, lokasi, sumber, tgl_exp]
            ['Kertas A4 80gr',        'Alat Tulis Kantor',  'Rim',  45000,  10, 25, 'Gudang ATK',   'APBD 2026',  null],
            ['Pulpen Hitam Pilot',    'Alat Tulis Kantor',  'Box',  35000,   5, 12, 'Gudang ATK',   'APBD 2026',  null],
            ['Spidol Whiteboard',     'Alat Tulis Kantor',  'Box',  28000,   3,  3, 'Gudang ATK',   'BOS 2026',   null], // menipis
            ['Penggaris 30cm',        'Alat Tulis Kantor',  'Pcs',   5000,   5,  0, 'Gudang ATK',   'APBD 2026',  null], // habis
            ['Stapler Besar',         'Alat Tulis Kantor',  'Pcs',  45000,   2,  7, 'Ruang Admin',  'APBD 2026',  null],
            ['Laptop Lenovo ThinkPad','Elektronik',         'Unit', 8500000, 1,  3, 'Ruang Kepala', 'APBD 2026',  null],
            ['Mouse Wireless Logitech','Elektronik',        'Pcs',  150000,  2,  5, 'Ruang Admin',  'APBD 2026',  null],
            ['Kabel HDMI 2m',         'Elektronik',        'Pcs',   75000,  2,  2, 'Gudang Utama', 'BOS 2026',   null], // menipis
            ['Proyektor Epson',       'Elektronik',        'Unit', 5500000, 1,  0, 'Ruang Kepala', 'APBD 2026',  null], // habis
            ['Flashdisk 32GB',        'Elektronik',        'Pcs',   85000,  5, 15, 'Gudang Utama', 'APBD 2026',  null],
            ['Sabun Cuci Tangan',     'Peralatan Kebersihan','Pack', 25000,  5,  8, 'Gudang Utama', 'BOS 2026',   $now->copy()->addDays(5)->toDateString()],  // akan expired
            ['Masker Medis',          'Peralatan Kebersihan','Box',  55000,  5,  4, 'Gudang Utama', 'BOS 2026',   $now->copy()->addDays(3)->toDateString()],  // akan expired
            ['Cairan Disinfektan',    'Peralatan Kebersihan','Pcs',  35000,  3,  1, 'Gudang Utama', 'BOS 2026',   $now->copy()->subDays(5)->toDateString()],  // sudah expired
            ['Tisu Meja',             'Peralatan Kebersihan','Pack', 18000,  10, 20, 'Gudang Utama', 'APBD 2026', $now->copy()->subDays(15)->toDateString()], // sudah expired
            ['Tinta Printer Hitam',   'Alat Tulis Kantor', 'Pcs',  125000,  3,  6, 'Gudang ATK',   'APBD 2026',  null],
            ['Map Arsip Snelhecter',  'Arsip',             'Pcs',    8000,  20, 45, 'Ruang Admin',  'APBD 2026',  null],
            ['Ordner A4 5cm',         'Arsip',             'Pcs',   18000,  10, 30, 'Ruang Admin',  'BOS 2026',   null],
            ['Kertas Label Stiker',   'Perlengkapan Rapat','Pack',  12000,   5,  5, 'Gudang ATK',   'BOS 2026',   null], // menipis
            ['Spanduk Banner 3x1',    'Perlengkapan Rapat','Pcs',  150000,   1,  0, 'Gudang Utama', 'Hibah',      null], // habis
            ['Whiteboard 120x90',     'Perlengkapan Rapat','Unit', 350000,   1,  2, 'Ruang Kepala', 'APBD 2026',  null],
        ];

        $itemObjs = [];
        foreach ($barangs as $i => $b) {
            [$nama, $katNama, $satNama, $harga, $min, $stok, $lokNama, $srcNama, $exp] = $b;
            $kode = 'BRG-' . str_pad($i + 10, 4, '0', STR_PAD_LEFT);
            $item = Item::firstOrCreate(
                ['kode_barang' => $kode],
                [
                    'nama_barang'        => $nama,
                    'kategori_id'        => $cats[$katNama]->id,
                    'satuan_kecil_id'    => $units[$satNama]->id,
                    'harga_satuan_kecil' => $harga,
                    'sumber_anggaran_id' => $sources[$srcNama]->id,
                    'lokasi_barang_id'   => $locs[$lokNama]->id,
                    'stok_minimal'       => $min,
                    'stok_saat_ini_kecil'=> $stok,
                    'stok_saat_ini_besar'=> 0,
                    'tgl_kadaluarsa'     => $exp,
                    'tgl_diterima'       => $now->copy()->subMonths(rand(1,6))->toDateString(),
                    'foto'               => null,
                ]
            );
            $itemObjs[] = $item;
        }

        // ── TRANSAKSI ────────────────────────────────────────────────
        $txMasuk = [
            [$itemObjs[0],  $wh1, 50,  0, $now->copy()->subDays(30)],
            [$itemObjs[1],  $wh1, 30,  0, $now->copy()->subDays(28)],
            [$itemObjs[2],  $wh1, 10,  0, $now->copy()->subDays(25)],
            [$itemObjs[4],  $wh1, 10,  0, $now->copy()->subDays(20)],
            [$itemObjs[5],  $wh2,  5,  0, $now->copy()->subDays(60)],
            [$itemObjs[6],  $wh1, 10,  0, $now->copy()->subDays(15)],
            [$itemObjs[9],  $wh1, 20,  0, $now->copy()->subDays(10)],
            [$itemObjs[10], $wh1, 20,  0, $now->copy()->subDays(8)],
            [$itemObjs[11], $wh1, 10,  0, $now->copy()->subDays(7)],
            [$itemObjs[14], $wh1, 10,  0, $now->copy()->subDays(5)],
        ];
        foreach ($txMasuk as $idx => [$item, $wh, $kecil, $besar, $tgl]) {
            StockTransaction::firstOrCreate(
                ['no_referensi' => 'MSK-DUMMY-' . str_pad($idx+1, 3, '0', STR_PAD_LEFT)],
                [
                    'barang_id'          => $item->id,
                    'gudang_id'          => $wh->id,
                    'pengguna_id'        => $user->id,
                    'jenis'              => 'masuk',
                    'jumlah_barang_kecil'=> $kecil,
                    'jumlah_barang_besar'=> $besar,
                    'keterangan'         => 'Penerimaan barang dummy',
                    'tgl_transaksi'      => $tgl,
                ]
            );
        }

        $txKeluar = [
            [$itemObjs[0],  $wh1, 25,  0, $now->copy()->subDays(20)],
            [$itemObjs[1],  $wh1, 18,  0, $now->copy()->subDays(18)],
            [$itemObjs[2],  $wh1,  7,  0, $now->copy()->subDays(14)],
            [$itemObjs[4],  $wh1,  3,  0, $now->copy()->subDays(12)],
            [$itemObjs[6],  $wh1,  5,  0, $now->copy()->subDays(10)],
            [$itemObjs[9],  $wh1,  5,  0, $now->copy()->subDays(6)],
            [$itemObjs[10], $wh1, 12,  0, $now->copy()->subDays(4)],
            [$itemObjs[11], $wh1,  6,  0, $now->copy()->subDays(3)],
            [$itemObjs[14], $wh1,  4,  0, $now->copy()->subDays(2)],
            [$itemObjs[15], $wh1, 15,  0, $now->copy()->subDays(1)],
        ];
        foreach ($txKeluar as $idx => [$item, $wh, $kecil, $besar, $tgl]) {
            StockTransaction::firstOrCreate(
                ['no_referensi' => 'KLR-DUMMY-' . str_pad($idx+1, 3, '0', STR_PAD_LEFT)],
                [
                    'barang_id'          => $item->id,
                    'gudang_id'          => $wh->id,
                    'pengguna_id'        => $user->id,
                    'jenis'              => 'keluar',
                    'jumlah_barang_kecil'=> $kecil,
                    'jumlah_barang_besar'=> $besar,
                    'keterangan'         => 'Pengeluaran barang dummy',
                    'tgl_transaksi'      => $tgl,
                ]
            );
        }

        // ── MUTASI GUDANG ────────────────────────────────────────────
        $mutasis = [
            [$itemObjs[0],  $wh1, $wh2, 10, $now->copy()->subDays(15)],
            [$itemObjs[5],  $wh2, $wh1,  1, $now->copy()->subDays(12)],
            [$itemObjs[9],  $wh1, $wh2,  5, $now->copy()->subDays(9)],
            [$itemObjs[15], $wh1, $wh2, 10, $now->copy()->subDays(6)],
            [$itemObjs[6],  $wh1, $wh2,  2, $now->copy()->subDays(3)],
        ];
        foreach ($mutasis as $idx => [$item, $from, $to, $jml, $tgl]) {
            StockMutation::firstOrCreate(
                ['no_mutasi' => 'MUT-DUMMY-' . str_pad($idx+1, 3, '0', STR_PAD_LEFT)],
                [
                    'barang_id'           => $item->id,
                    'gudang_asal_id'      => $from->id,
                    'gudang_tujuan_id'    => $to->id,
                    'pengguna_id'         => $user->id,
                    'jumlah_barang_kecil' => $jml,
                    'keterangan'          => 'Mutasi dummy',
                    'status'              => 'approved',
                    'tgl_mutasi'          => $tgl,
                ]
            );
        }

        // ── STOCK OPNAME ─────────────────────────────────────────────
        $opnames = [
            [$itemObjs[0],  $wh1, 25, 25,  0],
            [$itemObjs[1],  $wh1, 12, 10, -2],
            [$itemObjs[2],  $wh1,  3,  4,  1],
            [$itemObjs[9],  $wh1, 15, 15,  0],
            [$itemObjs[14], $wh1,  6,  5, -1],
        ];
        foreach ($opnames as [$item, $wh, $sistem, $fisik, $selisih]) {
            StockOpname::firstOrCreate(
                ['barang_id' => $item->id, 'gudang_id' => $wh->id, 'stok_sistem' => $sistem],
                [
                    'pengguna_id' => $user->id,
                    'stok_fisik'  => $fisik,
                    'selisih'     => $selisih,
                    'keterangan'  => $selisih == 0 ? 'Stok sesuai' : ($selisih > 0 ? 'Stok lebih' : 'Stok kurang'),
                ]
            );
        }

        // ── ACTIVITY LOG ─────────────────────────────────────────────
        $logs = [
            ['Login ke sistem',                          'Autentikasi'],
            ['Menambah barang: Kertas A4 80gr',          'Master Barang'],
            ['Menambah barang: Laptop Lenovo ThinkPad',  'Master Barang'],
            ['Transaksi masuk: Kertas A4 (50 Rim)',      'Inventory'],
            ['Transaksi keluar: Pulpen Hitam (18 Box)',  'Inventory'],
            ['Stock Opname: Kertas A4 — selisih 0',      'Inventory'],
            ['Mutasi gudang: Flashdisk 32GB (5 Pcs)',    'Inventory'],
            ['Memperbarui barang: Spidol Whiteboard',    'Master Barang'],
            ['Menghapus barang (Soft Delete): Ordner',   'Master Barang'],
            ['Menjalankan optimasi sistem',              'System'],
        ];
        foreach ($logs as [$act, $mod]) {
            ActivityLog::create([
                'pengguna_id' => $user->id,
                'activity'    => $act,
                'module'      => $mod,
                'ip_address'  => '127.0.0.1',
                'user_agent'  => 'Mozilla/5.0 (SIDARLOG Seeder)',
            ]);
        }

        // NOTE: Stok di-set langsung saat insert (tidak pakai recalculate
        // agar nilai dummy seperti stok menipis/habis tetap terjaga)

        $this->command->info('✓ DummyNotificationSeeder selesai.');
        $this->command->info('  Kategori: ' . count($cats));
        $this->command->info('  Barang: ' . count($itemObjs));
        $this->command->info('  Transaksi Masuk: ' . count($txMasuk));
        $this->command->info('  Transaksi Keluar: ' . count($txKeluar));
        $this->command->info('  Mutasi Gudang: ' . count($mutasis));
        $this->command->info('  Stock Opname: ' . count($opnames));
        $this->command->info('  Activity Log: ' . count($logs));
    }
}
