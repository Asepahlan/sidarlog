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
        $items         = Item::with(['satuanKecil', 'gudangPenyimpanan'])->orderBy('nama_barang')->get();
        $warehouses    = Warehouse::all();
        $firstParties  = \App\Models\FirstParty::all();
        $secondParties = \App\Models\SecondParty::all();
        $baps          = \App\Models\ReferenceBap::latest()->get();
        $users         = \App\Models\User::where('status_pegawai', 'Aktif')->orderBy('name')->get();

        return view('pages.transaksi.create', compact('items', 'warehouses', 'jenis', 'firstParties', 'secondParties', 'baps', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id'           => 'required|exists:items,id',
            'gudang_id'           => 'required|exists:warehouses,id',
            'jenis'               => 'required|in:masuk,keluar,penyesuaian',
            'jumlah_barang_kecil' => 'required|integer|min:1',
            'pihak_kesatu_id'     => 'nullable|exists:first_parties,id',
            'pihak_kedua_id'      => $request->input('jenis') === 'keluar' ? 'required|exists:second_parties,id' : 'nullable|exists:second_parties,id',
            'nomor_berita_acara'  => 'nullable|string|max:100',
            'penerima_id'         => 'nullable|exists:users,id',
            'penerima_penyerah'   => $request->input('jenis') === 'masuk' ? 'required|string|max:100' : 'nullable|string|max:255',
            'keterangan'          => 'nullable|string|max:1000',
            'tgl_transaksi'       => 'required|date',
        ]);

        // Ensure either penerima_id or pihak_kesatu_id is provided for inbound transactions
        if ($data['jenis'] === 'masuk' && empty($data['penerima_id']) && empty($data['pihak_kesatu_id'])) {
            return back()->withInput()->withErrors([
                'penerima_id' => 'Penerima wajib ditentukan (Pilih Penerima dari User Aktif atau Pihak Kesatu).',
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

    public function edit(Request $request, $id)
    {
        $routeName     = $request->route()->getName();
        $jenis         = str_contains($routeName, 'keluar') ? 'keluar' : 'masuk';
        $transaction   = StockTransaction::findOrFail($id);

        $items         = Item::with(['satuanKecil', 'gudangPenyimpanan'])->orderBy('nama_barang')->get();
        $warehouses    = Warehouse::all();
        $firstParties  = \App\Models\FirstParty::all();
        $secondParties = \App\Models\SecondParty::all();
        $baps          = \App\Models\ReferenceBap::latest()->get();
        $users         = \App\Models\User::where('status_pegawai', 'Aktif')->orderBy('name')->get();

        return view('pages.transaksi.edit', compact('transaction', 'items', 'warehouses', 'jenis', 'firstParties', 'secondParties', 'baps', 'users'));
    }

    public function update(Request $request, $id)
    {
        $transaction = StockTransaction::findOrFail($id);
        $data = $request->validate([
            'barang_id'           => 'required|exists:items,id',
            'gudang_id'           => 'required|exists:warehouses,id',
            'jumlah_barang_kecil' => 'required|integer|min:1',
            'pihak_kesatu_id'     => 'nullable|exists:first_parties,id',
            'pihak_kedua_id'      => $transaction->jenis === 'keluar' ? 'required|exists:second_parties,id' : 'nullable|exists:second_parties,id',
            'nomor_berita_acara'  => 'nullable|string|max:100',
            'penerima_id'         => 'nullable|exists:users,id',
            'penerima_penyerah'   => $transaction->jenis === 'masuk' ? 'required|string|max:100' : 'nullable|string|max:255',
            'keterangan'          => 'nullable|string|max:1000',
            'tgl_transaksi'       => 'required|date',
        ]);

        if ($transaction->jenis === 'masuk' && empty($data['penerima_id']) && empty($data['pihak_kesatu_id'])) {
            return back()->withInput()->withErrors([
                'penerima_id' => 'Penerima wajib ditentukan (Pilih Penerima dari User Aktif atau Pihak Kesatu).',
            ]);
        }

        try {
            $this->transactionService->updateTransaction($transaction, $data);
            return redirect()->route("barang-{$transaction->jenis}.index")
                ->with('success', 'Transaksi stok berhasil diperbarui.');

        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());

        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    public function setupBast($id)
    {
        $transaction = StockTransaction::with(['pihakKesatu', 'pihakKedua', 'penerima.jabatan'])->findOrFail($id);
        $transactions = StockTransaction::with(['barang.satuanKecil'])
            ->where('no_referensi', $transaction->no_referensi)
            ->get();

        $items = Item::with('satuanKecil')->orderBy('nama_barang')->get();
        $firstParties = \App\Models\FirstParty::all();
        $secondParties = \App\Models\SecondParty::all();

        return view('pages.transaksi.setup', compact('transaction', 'transactions', 'items', 'firstParties', 'secondParties'));
    }

    public function saveAndPrintBast(Request $request, $id)
    {
        $request->validate([
            'nomor_berita_acara' => 'required|string|max:100',
            'mode_tanggal' => 'required|in:otomatis,manual',
            'hari' => 'nullable|string|max:20',
            'tanggal' => 'nullable|string|max:50',
            'bulan' => 'nullable|string|max:30',
            'tahun' => 'nullable|string|max:30',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:items,id',
            'items.*.jumlah_barang_kecil' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        $mainTx = StockTransaction::findOrFail($id);

        try {
            DB::transaction(function () use ($request, $mainTx) {
                $requestedItems = $request->input('items');
                $reqBarangIds = collect($requestedItems)->pluck('barang_id')->toArray();

                // 1. Ambil transaksi yang sudah ada untuk no_referensi ini
                $existingTxs = StockTransaction::where('no_referensi', $mainTx->no_referensi)->get();
                $existingByBarang = $existingTxs->keyBy('barang_id');

                // 2. Hapus transaksi yang tidak dikirim di request (dan kembalikan stok)
                foreach ($existingTxs as $tx) {
                    if (!in_array($tx->barang_id, $reqBarangIds)) {
                        /** @var Item $item */
                        $item = Item::lockForUpdate()->findOrFail($tx->barang_id);
                        if ($tx->jenis === 'keluar') {
                            $item->stok_saat_ini_kecil += $tx->jumlah_barang_kecil;
                        } else {
                            $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil - $tx->jumlah_barang_kecil);
                        }
                        $item->save();
                        $tx->delete();
                    }
                }

                // 3. Proses item yang dikirim (tambah / update)
                foreach ($requestedItems as $reqItem) {
                    $barangId = $reqItem['barang_id'];
                    $qty = (int)$reqItem['jumlah_barang_kecil'];
                    $ket = $reqItem['keterangan'] ?? null;

                    /** @var Item $item */
                    $item = Item::lockForUpdate()->findOrFail($barangId);

                    if ($existingByBarang->has($barangId)) {
                        // UPDATE transaksi
                        $tx = $existingByBarang->get($barangId);
                        $oldQty = (int)$tx->jumlah_barang_kecil;
                        $diff = $qty - $oldQty;

                        if ($diff != 0) {
                            if ($tx->jenis === 'keluar') {
                                if ($item->getStockKecilInWarehouse($tx->gudang_id) < $diff) {
                                    throw new \Exception("Stok barang '{$item->nama_barang}' tidak mencukupi.");
                                }
                                $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil - $diff);
                            } else {
                                $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil + $diff);
                            }
                            $item->save();
                        }

                        $tx->update([
                            'jumlah_barang_kecil' => $qty,
                            'keterangan' => $ket,
                        ]);
                    } else {
                        // CREATE transaksi baru
                        if ($mainTx->jenis === 'keluar') {
                            if ($item->getStockKecilInWarehouse($mainTx->gudang_id) < $qty) {
                                throw new \Exception("Stok barang '{$item->nama_barang}' tidak mencukupi.");
                            }
                            $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil - $qty);
                        } else {
                            $item->stok_saat_ini_kecil += $qty;
                        }
                        $item->save();

                        StockTransaction::create([
                            'no_referensi' => $mainTx->no_referensi,
                            'barang_id' => $barangId,
                            'gudang_id' => $mainTx->gudang_id,
                            'pengguna_id' => $mainTx->pengguna_id,
                            'pihak_kesatu_id' => $mainTx->pihak_kesatu_id,
                            'pihak_kedua_id' => $mainTx->pihak_kedua_id,
                            'penerima_id' => $mainTx->penerima_id,
                            'penerima_penyerah' => $mainTx->penerima_penyerah,
                            'jenis' => $mainTx->jenis,
                            'jumlah_barang_kecil' => $qty,
                            'keterangan' => $ket,
                            'tgl_transaksi' => $mainTx->tgl_transaksi,
                        ]);
                    }
                }

                // 4. Update data metadata BAST untuk semua transaksi yang ber-referensi sama
                StockTransaction::where('no_referensi', $mainTx->no_referensi)->update([
                    'nomor_berita_acara' => $request->input('nomor_berita_acara'),
                    'mode_tanggal'       => $request->input('mode_tanggal'),
                    'hari'               => $request->input('hari'),
                    'tanggal'            => $request->input('tanggal'),
                    'bulan'              => $request->input('bulan'),
                    'tahun'              => $request->input('tahun'),
                    'kecamatan'          => $request->input('kecamatan'),
                    'desa'               => $request->input('desa'),
                    'catatan'            => $request->input('catatan'),
                    'penyerah_nama'      => $request->input('penyerah_nama'),
                    'penyerah_nip'       => $request->input('penyerah_nip'),
                    'penyerah_jabatan'   => $request->input('penyerah_jabatan'),
                    'penyerah_alamat'    => $request->input('penyerah_alamat'),
                ]);
            });

            // Stream / Download PDF setelah berhasil disimpan
            return $this->printBast($id);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses BAST: ' . $e->getMessage());
        }
    }

    public function printBast($id)
    {
        $transaction = StockTransaction::with([
            'barang.satuanKecil',
            'pihakKesatu', 'pihakKedua', 'gudang', 'pengguna', 'referenceBap', 'penerima.jabatan',
        ])->findOrFail($id);

        $transactions = StockTransaction::with(['barang.satuanKecil'])
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
                } elseif ($transaction->jenis === 'masuk') {
                    // Transaksi masuk dibatalkan → stok dikurangi (tidak boleh minus)
                    $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil - (int) $transaction->jumlah_barang_kecil);
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
