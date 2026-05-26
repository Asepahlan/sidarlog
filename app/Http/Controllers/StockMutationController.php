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
    public function index()
    {
        $mutations = StockMutation::with(['barang', 'gudangAsal', 'gudangTujuan', 'pembuat'])
            ->latest()
            ->paginate(15);
        return view('pages.gudang.mutasi', compact('mutations'));
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

        $item = Item::findOrFail($request->barang_id);
        $stockKecilDiGudangAsal = $item->getStockKecilInWarehouse($request->gudang_asal_id);

        if ($request->jumlah_barang_kecil > $stockKecilDiGudangAsal) {
            return redirect()->back()->withInput()->with('error', "Stok barang di gudang asal tidak mencukupi. Sisa stok: {$stockKecilDiGudangAsal}");
        }

        DB::beginTransaction();
        try {
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

            ActivityLog::log(
                "Mutasi stok {$request->jumlah_barang_kecil} unit barang ID#{$request->barang_id} dari gudang #{$request->gudang_asal_id} ke #{$request->gudang_tujuan_id}",
                "Mutasi Gudang",
                $request->all()
            );

            DB::commit();
            return redirect()->route('mutasi-gudang.index')->with('success', "Mutasi gudang {$noMutasi} berhasil dicatat!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan mutasi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $mutation = StockMutation::findOrFail($id);
        ActivityLog::log("Menghapus Mutasi: {$mutation->no_mutasi}", "Mutasi Gudang");
        $mutation->delete();
        return redirect()->back()->with('success', 'Data mutasi berhasil dihapus');
    }
}
