<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::first();
        $unit = Unit::first();
        $wh = Warehouse::first();
        $user = User::first();

        $item1 = Item::create([
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Kertas A4 80gr',
            'kategori_id' => $cat->id,
            'satuan_kecil_id' => $unit->id,
            'stok_minimal' => 10,
            'deskripsi' => 'Kertas print standar',
        ]);

        StockTransaction::create([
            'no_referensi' => 'BM-SAMPLE-01',
            'barang_id' => $item1->id,
            'gudang_id' => $wh->id,
            'pengguna_id' => $user->id,
            'jenis' => 'masuk',
            'jumlah_barang_kecil' => 50,
            'tgl_transaksi' => now(),
        ]);

        $item2 = Item::create([
            'kode_barang' => 'BRG-002',
            'nama_barang' => 'Tinta Printer Hitam',
            'kategori_id' => $cat->id,
            'satuan_kecil_id' => $unit->id,
            'stok_minimal' => 5,
            'deskripsi' => 'Tinta original',
        ]);
        
        // Add sample activity logs
        \App\Models\ActivityLog::create([
            'pengguna_id' => $user->id,
            'activity' => 'Inisialisasi Sistem',
            'module' => 'Sistem',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0'
        ]);

        $this->command->info('Sample data added.');
    }
}
