<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StockTransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $routeName = $request->route()->getName();
        $jenis     = str_contains($routeName, 'keluar') ? 'keluar' : 'masuk';

        $transactions = StockTransaction::with(['barang', 'gudang', 'pengguna', 'pihakKesatu', 'pihakKedua'])
            ->where('jenis', $jenis)
            ->orderBy('tgl_transaksi', 'desc')
            ->paginate(15);

        return view('pages.transaksi.index', compact('transactions', 'jenis'));
    }

    public function create(Request $request)
    {
        $routeName    = $request->route()->getName();
        $jenis        = str_contains($routeName, 'keluar') ? 'keluar' : 'masuk';
        $items        = Item::with(['unitKecil', 'unitBesar'])->orderBy('nama_barang')->get();
        $warehouses   = Warehouse::all();
        $firstParties = \App\Models\FirstParty::all();
        $secondParties = \App\Models\SecondParty::all();

        return view('pages.transaksi.create', compact('items', 'warehouses', 'jenis', 'firstParties', 'secondParties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id'         => 'required|exists:items,id',
            'gudang_id'         => 'required|exists:warehouses,id',
            'jenis'             => 'required|in:masuk,keluar,penyesuaian',
            'jumlah_barang_kecil' => 'nullable|integer|min:0',
            'jumlah_barang_besar' => 'nullable|integer|min:0',
            'pihak_kesatu_id'   => 'nullable|exists:first_parties,id',
            'pihak_kedua_id'    => 'nullable|exists:second_parties,id',
            'penerima_penyerah' => 'nullable|string|max:255',
            'keterangan'        => 'nullable|string|max:1000',
            'tgl_transaksi'     => 'required|date',
        ]);

        // Ensure at least one quantity is provided
        if (empty($data['jumlah_barang_kecil']) && empty($data['jumlah_barang_besar'])) {
            return back()->withInput()->withErrors([
                'jumlah_barang_kecil' => 'Jumlah barang (kecil atau besar) wajib diisi minimal salah satu.',
            ]);
        }

        try {
            $this->transactionService->recordTransaction($data);
            $jenis = $data['jenis'] === 'masuk' ? 'masuk' : 'keluar';
            return redirect()->route("barang-{$jenis}.index")
                ->with('success', 'Transaksi stok berhasil dicatat.');

        } catch (ValidationException $e) {
            // User-friendly validation errors from service (e.g. stok tidak cukup)
            return back()->withInput()->withErrors($e->errors());

        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    public function printBast($id)
    {
        $transaction = StockTransaction::with([
            'barang.satuanKecil', 'barang.satuanBesar',
            'pihakKesatu', 'pihakKedua', 'gudang', 'pengguna',
        ])->findOrFail($id);

        $transactions = StockTransaction::with(['barang.satuanKecil', 'barang.satuanBesar'])
            ->where('no_referensi', $transaction->no_referensi)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.bast', compact('transaction', 'transactions'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('BAST-' . $transaction->no_referensi . '.pdf');
    }

    public function destroy($id)
    {
        try {
            $transaction = StockTransaction::findOrFail($id);
            $jenis = $transaction->jenis;
            $noRef = $transaction->no_referensi;

            ActivityLog::log("Menghapus Transaksi {$jenis}: {$noRef}", "Inventory");
            $transaction->delete();

            return back()->with('success', 'Data transaksi berhasil dihapus.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
