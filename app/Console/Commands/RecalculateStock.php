<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Console\Command;

class RecalculateStock extends Command
{
    protected $signature   = 'stock:recalculate {--item= : ID spesifik item (opsional)}';
    protected $description = 'Recalculate and sync stok_saat_ini_kecil/besar from stock transactions';

    public function handle(): int
    {
        $query = Item::query();

        if ($id = $this->option('item')) {
            $query->where('id', $id);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            $this->warn('Tidak ada item ditemukan.');
            return 1;
        }

        $this->info("Menghitung ulang stok untuk {$items->count()} item...");
        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        foreach ($items as $item) {
            $kecil = $this->calcStock($item->id, 'jumlah_barang_kecil');
            $besar = $this->calcStock($item->id, 'jumlah_barang_besar');

            $item->stok_saat_ini_kecil = $kecil;
            $item->stok_saat_ini_besar = $besar;
            $item->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✓ Stok cache berhasil diperbarui.');
        return 0;
    }

    private function calcStock(int $itemId, string $col): int
    {
        $masuk = StockTransaction::where('barang_id', $itemId)
            ->where('jenis', 'masuk')->sum($col);
        $keluar = StockTransaction::where('barang_id', $itemId)
            ->where('jenis', 'keluar')->sum($col);
        $penyesuaian = StockTransaction::where('barang_id', $itemId)
            ->where('jenis', 'penyesuaian')->sum($col);

        return (int) max(0, $masuk - $keluar + $penyesuaian);
    }
}
