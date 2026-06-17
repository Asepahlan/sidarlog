<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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

        $transactions = StockTransaction::with(['barang', 'gudang', 'pengguna', 'pihakKesatu', 'pihakKedua', 'referenceBap'])
            ->where('jenis', $jenis)
            ->orderBy('tgl_transaksi', 'desc')
            ->paginate(15);

        return view('pages.transaksi.index', compact('transactions', 'jenis'));
    }

    public function create(Request $request)
    {
        $routeName     = $request->route()->getName();
        $jenis         = str_contains($routeName, 'keluar') ? 'keluar' : 'masuk';
        $items         = Item::with(['unitKecil', 'unitBesar'])->orderBy('nama_barang')->get();
        $warehouses    = Warehouse::all();
        $firstParties  = \App\Models\FirstParty::all();
        $secondParties = \App\Models\SecondParty::all();
        $baps          = \App\Models\ReferenceBap::latest()->get();

        return view('pages.transaksi.create', compact('items', 'warehouses', 'jenis', 'firstParties', 'secondParties', 'baps'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id'           => 'required|exists:items,id',
            'gudang_id'           => 'required|exists:warehouses,id',
            'jenis'               => 'required|in:masuk,keluar,penyesuaian',
            'jumlah_barang_kecil' => 'nullable|integer|min:0',
            'jumlah_barang_besar' => 'nullable|integer|min:0',
            'pihak_kesatu_id'     => 'nullable|exists:first_parties,id',
            'pihak_kedua_id'      => 'nullable|exists:second_parties,id',
            'reference_bap_id'    => 'nullable|exists:reference_baps,id',
            'penerima_penyerah'   => 'nullable|string|max:255',
            'keterangan'          => 'nullable|string|max:1000',
            'tgl_transaksi'       => 'required|date',
        ]);

        // Ensure either pihak_kedua_id or penerima_penyerah is provided for outbound transactions
        if ($data['jenis'] === 'keluar' && empty($data['pihak_kedua_id']) && empty($data['penerima_penyerah'])) {
            return back()->withInput()->withErrors([
                'pihak_kedua_id' => 'Penerima wajib ditentukan (Pilih Pihak Kedua atau isi Nama Penerima Lainnya).',
            ]);
        }

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
            'pihakKesatu', 'pihakKedua', 'gudang', 'pengguna', 'referenceBap',
        ])->findOrFail($id);

        $transactions = StockTransaction::with(['barang.satuanKecil', 'barang.satuanBesar'])
            ->where('no_referensi', $transaction->no_referensi)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.bast', compact('transaction', 'transactions'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('BAST-' . $transaction->no_referensi . '.pdf');
    }

    /**
     * Batalkan transaksi dan kembalikan stok barang.
     * Menggunakan DB::transaction + lockForUpdate untuk keamanan concurrency.
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                /** @var StockTransaction $transaction */
                $transaction = StockTransaction::lockForUpdate()->findOrFail($id);

                // Jangan izinkan pembatalan transaksi penyesuaian opname secara manual
                if ($transaction->jenis === 'penyesuaian') {
                    throw new \Exception('Transaksi penyesuaian opname tidak dapat dibatalkan secara manual. Lakukan Stock Opname ulang untuk memperbaiki stok.');
                }

                /** @var Item $item */
                $item = Item::lockForUpdate()->findOrFail($transaction->barang_id);

                // Rollback stok berdasarkan jenis transaksi
                if ($transaction->jenis === 'keluar') {
                    // Transaksi keluar dibatalkan → stok dikembalikan ke gudang
                    $item->stok_saat_ini_kecil += (int) $transaction->jumlah_barang_kecil;
                    $item->stok_saat_ini_besar += (int) $transaction->jumlah_barang_besar;
                } elseif ($transaction->jenis === 'masuk') {
                    // Transaksi masuk dibatalkan → stok dikurangi (tidak boleh minus)
                    $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil - (int) $transaction->jumlah_barang_kecil);
                    $item->stok_saat_ini_besar = max(0, $item->stok_saat_ini_besar - (int) $transaction->jumlah_barang_besar);
                }

                $item->save();

                // Observer StockTransactionObserver::deleted() mencatat log otomatis
                $transaction->delete();
            });

            return back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
