<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\StockTransaction;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\ItemRepositoryInterface;

class TestSidarlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sidarlog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifikasi dan jalankan simulasi operasional hasil audit sistem SIDARLOG';

    protected $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        parent::__construct();
        $this->itemRepository = $itemRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== MEMULAI SIMULASI & VERIFIKASI OPERASIONAL SIDARLOG ===");

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->error("Gagal terhubung ke database. Harap aktifkan server database MySQL Anda terlebih dahulu.");
            $this->error("Detail error: " . $e->getMessage());
            return 1;
        }

        // Jalankan uji coba
        $this->testRestrictDelete();
        $this->testAutomaticLogging();
        $this->testConcurrencyLock();

        $this->info("\n=== VERIFIKASI SELESAI ===");
    }

    private function testRestrictDelete()
    {
        $this->info("\n[TEST 1] Verifikasi Restrict Delete Constraint:");
        
        DB::beginTransaction();

        try {
            // 1. Buat data dummy
            $cat = Category::create(['nama_kategori' => 'Kategori Uji ' . rand(100, 999)]);
            $unit = Unit::create(['nama_satuan' => 'Pcs Uji', 'simbol' => 'PU']);
            $wh = Warehouse::create(['nama_gudang' => 'Gudang Uji', 'kode_gudang' => 'WH-TEST-' . rand(100, 999)]);
            
            $item = Item::create([
                'kode_barang' => 'BRG-TST-' . rand(100, 999),
                'nama_barang' => 'Barang Uji Restrict',
                'kategori_id' => $cat->id,
                'satuan_kecil_id' => $unit->id,
                'stok_minimal' => 5,
            ]);

            $tx = StockTransaction::create([
                'no_referensi' => 'TX-TEST-' . rand(100, 999),
                'barang_id' => $item->id,
                'gudang_id' => $wh->id,
                'pengguna_id' => auth()->id() ?? 1,
                'jenis' => 'masuk',
                'jumlah_barang_kecil' => 10,
                'jumlah_barang_besar' => 0,
                'tgl_transaksi' => now(),
            ]);

            $this->line("Data dummy berhasil dibuat. Mencoba melakukan hapus permanen (forceDelete) pada barang...");

            // 2. Coba hapus permanen barang yang memiliki transaksi
            try {
                $item->forceDelete();
                $this->error("GAGAL: Barang berhasil dihapus permanen! Constraint cascade delete masih aktif.");
            } catch (\Illuminate\Database\QueryException $e) {
                $this->info("SUKSES: Database menolak penghapusan permanen karena barang memiliki transaksi terkait.");
                $this->line("Pesan error database: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan saat uji coba: " . $e->getMessage());
        } finally {
            DB::rollBack();
            $this->line("Transaksi uji coba berhasil di-rollback.");
        }
    }

    private function testAutomaticLogging()
    {
        $this->info("\n[TEST 2] Verifikasi Pencatatan Log Otomatis (Observer):");

        DB::beginTransaction();

        try {
            $cat = Category::create(['nama_kategori' => 'Kategori Uji ' . rand(100, 999)]);
            $unit = Unit::create(['nama_satuan' => 'Pcs Uji', 'simbol' => 'PU']);

            $logBefore = ActivityLog::count();

            // Buat item (seharusnya mentrigger ItemObserver@created)
            $item = Item::create([
                'kode_barang' => 'BRG-LOG-' . rand(100, 999),
                'nama_barang' => 'Barang Uji Observer',
                'kategori_id' => $cat->id,
                'satuan_kecil_id' => $unit->id,
                'stok_minimal' => 5,
            ]);

            $logAfter = ActivityLog::count();

            if ($logAfter > $logBefore) {
                $latestLog = ActivityLog::latest()->first();
                $this->info("SUKSES: Log aktivitas berhasil dicatat secara otomatis oleh Observer!");
                $this->line("Log terbaru: \"" . $latestLog->activity . "\" (Kategori: " . $latestLog->module . ")");
            } else {
                $this->error("GAGAL: Tidak ada log aktivitas baru yang dicatat ketika barang dibuat.");
            }

        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan saat uji coba log: " . $e->getMessage());
        } finally {
            DB::rollBack();
            $this->line("Transaksi uji coba berhasil di-rollback.");
        }
    }

    private function testConcurrencyLock()
    {
        $this->info("\n[TEST 3] Verifikasi Kompilasi & Kode Concurrency Lock:");

        DB::beginTransaction();

        try {
            $cat = Category::create(['nama_kategori' => 'Kategori Uji ' . rand(100, 999)]);
            $unit = Unit::create(['nama_satuan' => 'Pcs Uji', 'simbol' => 'PU']);
            
            $item = Item::create([
                'kode_barang' => 'BRG-LCK-' . rand(100, 999),
                'nama_barang' => 'Barang Uji Lock',
                'kategori_id' => $cat->id,
                'satuan_kecil_id' => $unit->id,
                'stok_minimal' => 5,
            ]);

            $this->line("Mencoba memanggil findWithLock() pada ItemRepository...");
            
            $lockedItem = $this->itemRepository->findWithLock($item->id);

            if ($lockedItem && $lockedItem->id === $item->id) {
                $this->info("SUKSES: Metode findWithLock() berhasil dieksekusi dan mengunci baris barang.");
            } else {
                $this->error("GAGAL: Hasil findWithLock() tidak valid.");
            }

        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan saat menguji lock: " . $e->getMessage());
        } finally {
            DB::rollBack();
            $this->line("Transaksi uji coba berhasil di-rollback.");
        }
    }
}
