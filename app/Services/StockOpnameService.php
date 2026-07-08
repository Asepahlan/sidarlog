<?php

namespace App\Services;

use App\Repositories\Interfaces\StockOpnameRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Models\ActivityLog;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StockOpnameService
{
    protected $opnameRepository;
    protected $transactionRepository;
    protected $itemRepository;

    public function __construct(
        StockOpnameRepositoryInterface $opnameRepository,
        TransactionRepositoryInterface $transactionRepository,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->opnameRepository = $opnameRepository;
        $this->transactionRepository = $transactionRepository;
        $this->itemRepository = $itemRepository;
    }

    public function getAllOpnames()
    {
        return $this->opnameRepository->all(['barang', 'gudang', 'pengguna']);
    }

    public function processOpname(array $data)
    {
        return DB::transaction(function () use ($data) {
            /** @var Item $item */
            $item = $this->itemRepository->findWithLock($data['barang_id']);

            // Menggunakan stok spesifik gudang yang bersangkutan
            $stokSistem = $item->getStockKecilInWarehouse($data['gudang_id']);
            $stokFisik  = (int) $data['stok_fisik'];
            $selisih    = $stokFisik - $stokSistem;

            // 1. Save Opname Record
            $opname = $this->opnameRepository->create([
                'barang_id'    => $data['barang_id'],
                'gudang_id'    => $data['gudang_id'],
                'pengguna_id'  => Auth::id(),
                'stok_sistem'  => $stokSistem,
                'stok_fisik'   => $stokFisik,
                'selisih'      => $selisih,
                'keterangan'   => $data['keterangan'] ?? 'Stock Opname',
            ]);

            // 2. Create Adjustment Transaction & sync stock cache if there is a difference
            if ($selisih !== 0) {
                $this->transactionRepository->create([
                    'barang_id'     => $data['barang_id'],
                    'gudang_id'     => $data['gudang_id'],
                    'pengguna_id'       => Auth::id(),
                    'jenis'         => 'penyesuaian',
                    'jumlah_barang_kecil'  => $selisih,
                    'no_referensi'  => 'ADJ-' . now()->format('YmdHis'),
                    'tgl_transaksi' => now(),
                    'keterangan'    => "Penyesuaian otomatis dari Stock Opname #{$opname->id}",
                ]);

                // 3. Sync stock cache on the items table
                $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil + $selisih);
                $item->save();
            }

            \App\Services\NotificationService::checkAndNotifyForItem($item);

            return $opname;
        });
    }

    public function saveBatchOpname(array $data)
    {
        return DB::transaction(function () use ($data) {
            $gudangId = $data['gudang_id'];
            $nomorSo  = $data['nomor_stock_opname'];
            $periode  = $data['periode'];
            $tglOpname = $data['tgl_opname'];
            
            $petugasNama     = $data['petugas_nama'] ?? null;
            $petugasNip      = $data['petugas_nip'] ?? null;
            $kepalaGudangNama = $data['kepala_gudang_nama'] ?? null;
            $kepalaGudangNip  = $data['kepala_gudang_nip'] ?? null;
            $mengetahuiNama  = $data['mengetahui_nama'] ?? null;
            $mengetahuiNip   = $data['mengetahui_nip'] ?? null;
            
            $opnameIds = [];

            foreach ($data['items'] as $itemData) {
                $barangId    = $itemData['barang_id'];
                $stokFisik   = (int) $itemData['stok_fisik'];
                $stokSistem  = (int) $itemData['stok_sistem'];
                $kondisi     = $itemData['kondisi_barang'] ?? 'Baik';
                $keterangan  = $itemData['keterangan'] ?? null;
                $selisih     = $stokFisik - $stokSistem;

                /** @var Item $item */
                $item = $this->itemRepository->findWithLock($barangId);

                // Cari apakah sudah ada record opname dengan nomor_stock_opname & barang_id ini
                $existingOpname = \App\Models\StockOpname::where('nomor_stock_opname', $nomorSo)
                    ->where('barang_id', $barangId)
                    ->first();

                if ($existingOpname) {
                    // Update
                    $oldStokFisik = (int) $existingOpname->stok_fisik;
                    $diff = $stokFisik - $oldStokFisik;

                    $existingOpname->update([
                        'periode'            => $periode,
                        'tgl_opname'         => $tglOpname,
                        'gudang_id'          => $gudangId,
                        'pengguna_id'        => Auth::id(),
                        'stok_sistem'        => $stokSistem,
                        'stok_fisik'         => $stokFisik,
                        'selisih'            => $selisih,
                        'kondisi_barang'     => $kondisi,
                        'keterangan'         => $keterangan,
                        'petugas_nama'       => $petugasNama,
                        'petugas_nip'        => $petugasNip,
                        'kepala_gudang_nama' => $kepalaGudangNama,
                        'kepala_gudang_nip'  => $kepalaGudangNip,
                        'mengetahui_nama'    => $mengetahuiNama,
                        'mengetahui_nip'     => $mengetahuiNip,
                    ]);

                    // Jika ada perbedaan stok fisik dari input sebelumnya, update stok item & penyesuaian transaction
                    if ($diff !== 0) {
                        $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil + $diff);
                        $item->save();

                        // Cari transaksi penyesuaian terkait untuk di-update
                        $adjTx = \App\Models\StockTransaction::where('no_referensi', 'ADJ-SO-' . $existingOpname->id)
                            ->first();

                        if ($adjTx) {
                            $adjTx->update([
                                'jumlah_barang_kecil' => $selisih,
                                'keterangan' => "Penyesuaian otomatis dari Stock Opname #{$existingOpname->id} ($nomorSo)",
                            ]);
                        } else if ($selisih !== 0) {
                            // Buat baru jika sebelumnya selisih 0 tapi sekarang ada selisih
                            \App\Models\StockTransaction::create([
                                'barang_id'           => $barangId,
                                'gudang_id'           => $gudangId,
                                'pengguna_id'         => Auth::id(),
                                'jenis'               => 'penyesuaian',
                                'jumlah_barang_kecil' => $selisih,
                                'no_referensi'        => 'ADJ-SO-' . $existingOpname->id,
                                'tgl_transaksi'       => $tglOpname ?? now(),
                                'keterangan'          => "Penyesuaian otomatis dari Stock Opname #{$existingOpname->id} ($nomorSo)",
                            ]);
                        }
                    } else {
                        // Jika selisihnya berubah karena stok sistem berubah tapi fisik sama, update jumlah_barang_kecil saja
                        $adjTx = \App\Models\StockTransaction::where('no_referensi', 'ADJ-SO-' . $existingOpname->id)
                            ->first();
                        if ($adjTx) {
                            if ($selisih === 0) {
                                $adjTx->delete();
                            } else {
                                $adjTx->update([
                                    'jumlah_barang_kecil' => $selisih,
                                ]);
                            }
                        } else if ($selisih !== 0) {
                            \App\Models\StockTransaction::create([
                                'barang_id'           => $barangId,
                                'gudang_id'           => $gudangId,
                                'pengguna_id'         => Auth::id(),
                                'jenis'               => 'penyesuaian',
                                'jumlah_barang_kecil' => $selisih,
                                'no_referensi'        => 'ADJ-SO-' . $existingOpname->id,
                                'tgl_transaksi'       => $tglOpname ?? now(),
                                'keterangan'          => "Penyesuaian otomatis dari Stock Opname #{$existingOpname->id} ($nomorSo)",
                            ]);
                        }
                    }

                    $opnameIds[] = $existingOpname->id;

                } else {
                    // Create baru
                    $opname = \App\Models\StockOpname::create([
                        'nomor_stock_opname' => $nomorSo,
                        'periode'            => $periode,
                        'tgl_opname'         => $tglOpname,
                        'barang_id'          => $barangId,
                        'gudang_id'          => $gudangId,
                        'pengguna_id'        => Auth::id(),
                        'stok_sistem'        => $stokSistem,
                        'stok_fisik'         => $stokFisik,
                        'selisih'            => $selisih,
                        'kondisi_barang'     => $kondisi,
                        'keterangan'         => $keterangan,
                        'petugas_nama'       => $petugasNama,
                        'petugas_nip'        => $petugasNip,
                        'kepala_gudang_nama' => $kepalaGudangNama,
                        'kepala_gudang_nip'  => $kepalaGudangNip,
                        'mengetahui_nama'    => $mengetahuiNama,
                        'mengetahui_nip'     => $mengetahuiNip,
                    ]);

                    if ($selisih !== 0) {
                        \App\Models\StockTransaction::create([
                            'barang_id'           => $barangId,
                            'gudang_id'           => $gudangId,
                            'pengguna_id'         => Auth::id(),
                            'jenis'               => 'penyesuaian',
                            'jumlah_barang_kecil' => $selisih,
                            'no_referensi'        => 'ADJ-SO-' . $opname->id,
                            'tgl_transaksi'       => $tglOpname ?? now(),
                            'keterangan'          => "Penyesuaian otomatis dari Stock Opname #{$opname->id} ($nomorSo)",
                        ]);

                        $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil + $selisih);
                        $item->save();
                    }

                    \App\Services\NotificationService::checkAndNotifyForItem($item);

                    $opnameIds[] = $opname->id;
                }
            }

            return $opnameIds;
        });
    }
}
