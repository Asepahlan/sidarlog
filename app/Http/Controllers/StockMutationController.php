<?php

namespace App\Http\Controllers;

use App\Models\StockMutation;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMutationController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMutation::with(['barang', 'gudangAsal', 'gudangTujuan', 'pembuat']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_mutasi', 'like', '%' . $search . '%')
                  ->orWhereHas('barang', function ($itemQuery) use ($search) {
                      $itemQuery->where('nama_barang', 'like', '%' . $search . '%')
                                ->orWhere('kode_barang', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('gudang_asal_id')) {
            $query->where('gudang_asal_id', $request->gudang_asal_id);
        }

        if ($request->filled('gudang_tujuan_id')) {
            $query->where('gudang_tujuan_id', $request->gudang_tujuan_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_mutasi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_mutasi', '<=', $request->end_date);
        }

        $mutations = $query->latest()->paginate(15);
        $warehouses = Warehouse::all();

        return view('pages.gudang.mutasi', compact('mutations', 'warehouses'));
    }

    public function create()
    {
        $items = Item::orderBy('nama_barang')->get();
        $warehouses = Warehouse::all();
        return view('pages.gudang.mutasi_create', compact('items', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'         => 'required|exists:items,id',
            'gudang_asal_id'    => 'required|exists:warehouses,id',
            'gudang_tujuan_id'  => 'required|exists:warehouses,id|different:gudang_asal_id',
            'jumlah_barang_kecil' => 'required|integer|min:1',
            'tgl_mutasi'        => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $item = Item::lockForUpdate()->findOrFail($request->barang_id);
            $stockKecilDiGudangAsal = $item->getStockKecilInWarehouse($request->gudang_asal_id);

            if ($request->jumlah_barang_kecil > $stockKecilDiGudangAsal) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', "Stok barang di gudang asal tidak mencukupi. Sisa stok: {$stockKecilDiGudangAsal}");
            }

            $noMutasi = 'MUT-' . date('Ymd') . '-' . str_pad(StockMutation::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $mutation = StockMutation::create([
                'no_mutasi'          => $noMutasi,
                'barang_id'          => $request->barang_id,
                'gudang_asal_id'     => $request->gudang_asal_id,
                'gudang_tujuan_id'   => $request->gudang_tujuan_id,
                'pengguna_id'        => auth()->id(),
                'jumlah_barang_kecil'=> $request->jumlah_barang_kecil,
                'keterangan'         => $request->keterangan,
                'status'             => 'approved',
                'tgl_mutasi'         => $request->tgl_mutasi,
            ]);

            // Catat transaksi keluar dari gudang asal
            StockTransaction::create([
                'no_referensi'     => $noMutasi . '-OUT',
                'barang_id'        => $request->barang_id,
                'gudang_id'        => $request->gudang_asal_id,
                'pengguna_id'      => auth()->id(),
                'jenis'            => 'keluar',
                'jumlah_barang_kecil' => $request->jumlah_barang_kecil,
                'penerima_penyerah'=> 'Mutasi ke Gudang #' . $request->gudang_tujuan_id,
                'keterangan'       => 'Mutasi: ' . $request->keterangan,
                'tgl_transaksi'    => $request->tgl_mutasi,
            ]);

            // Catat transaksi masuk ke gudang tujuan
            StockTransaction::create([
                'no_referensi'     => $noMutasi . '-IN',
                'barang_id'        => $request->barang_id,
                'gudang_id'        => $request->gudang_tujuan_id,
                'pengguna_id'      => auth()->id(),
                'jenis'            => 'masuk',
                'jumlah_barang_kecil' => $request->jumlah_barang_kecil,
                'penerima_penyerah'=> 'Mutasi dari Gudang #' . $request->gudang_asal_id,
                'keterangan'       => 'Mutasi: ' . $request->keterangan,
                'tgl_transaksi'    => $request->tgl_mutasi,
            ]);

            DB::commit();
            \App\Services\NotificationService::checkAndNotifyForItem($item);
            return redirect()->route('mutasi-gudang.index')->with('success', "Mutasi gudang {$noMutasi} berhasil dicatat!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan mutasi: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan mutasi: hapus transaksi IN/OUT terkait, rollback stok, dan catat log.
     * Menggunakan DB::transaction + lockForUpdate untuk keamanan concurrency.
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                /** @var StockMutation $mutation */
                $mutation = StockMutation::lockForUpdate()->findOrFail($id);

                /** @var Item $item */
                $item = Item::lockForUpdate()->findOrFail($mutation->barang_id);

                $jumlah = (int) $mutation->jumlah_barang_kecil;

                // Verifikasi stok gudang tujuan mencukupi untuk rollback
                $stokDiTujuan = $item->getStockKecilInWarehouse($mutation->gudang_tujuan_id);
                if ($stokDiTujuan < $jumlah) {
                    throw new \Exception(
                        "Tidak dapat membatalkan mutasi. Stok di gudang tujuan ({$stokDiTujuan}) " .
                        "tidak mencukupi untuk dikembalikan ({$jumlah})."
                    );
                }

                // Hapus transaksi keluar (dari gudang asal) → stok asal otomatis kembali
                $txOut = StockTransaction::where('no_referensi', $mutation->no_mutasi . '-OUT')->first();
                if ($txOut) {
                    // Rollback: tambahkan kembali ke stok global (karena transaksi OUT sudah dikurangi)
                    $item->stok_saat_ini_kecil += (int) $txOut->jumlah_barang_kecil;
                    $txOut->delete();
                }

                // Hapus transaksi masuk (ke gudang tujuan) → stok tujuan dikurangi
                $txIn = StockTransaction::where('no_referensi', $mutation->no_mutasi . '-IN')->first();
                if ($txIn) {
                    // Rollback: kurangi dari stok global (karena transaksi IN sudah menambah)
                    $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil - (int) $txIn->jumlah_barang_kecil);
                    $txIn->delete();
                }

                $item->save();
                \App\Services\NotificationService::checkAndNotifyForItem($item);

                $mutation->delete();
            });

            return redirect()->back()->with('success', 'Mutasi berhasil dibatalkan dan stok telah disesuaikan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan mutasi: ' . $e->getMessage());
        }
    }
}
