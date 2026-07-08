<?php

namespace App\Http\Controllers;

use App\Services\StockOpnameService;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\FirstParty;
use App\Models\User;
use App\Models\StockOpname;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OpnameExport;


class StockOpnameController extends Controller
{
    protected $opnameService;

    public function __construct(StockOpnameService $opnameService)
    {
        $this->opnameService = $opnameService;
    }

    public function index(Request $request)
    {
        $allWarehouses = Warehouse::all();
        $categories = Category::all();
        $firstParties = FirstParty::all();
        $users = User::all();

        // Hitung ringkasan stok aktif per gudang untuk petunjuk di dropdown
        $stockSummaryByWarehouse = StockTransaction::select('gudang_id', DB::raw('SUM(CASE WHEN jenis = "masuk" THEN jumlah_barang_kecil WHEN jenis = "penyesuaian" THEN jumlah_barang_kecil ELSE 0 END) - SUM(CASE WHEN jenis = "keluar" THEN jumlah_barang_kecil ELSE 0 END) as stok_total'))
            ->groupBy('gudang_id')
            ->pluck('stok_total', 'gudang_id');

        $warehouses = $allWarehouses->map(function ($wh) use ($stockSummaryByWarehouse) {
            $wh->stok_total = $stockSummaryByWarehouse[$wh->id] ?? 0;
            return $wh;
        });

        // Ambil riwayat/daftar dokumen stock opname yang unik untuk tab daftar laporan
        $historyOpnames = StockOpname::with(['gudang'])
            ->select('nomor_stock_opname', 'periode', 'tgl_opname', 'gudang_id', 'petugas_nama', 'kepala_gudang_nama')
            ->whereNotNull('nomor_stock_opname')
            ->groupBy('nomor_stock_opname', 'periode', 'tgl_opname', 'gudang_id', 'petugas_nama', 'kepala_gudang_nama')
            ->latest('tgl_opname')
            ->paginate(15);

        // State filter pencarian aktif
        $items = collect();
        $existingOpnames = collect();
        $metadata = [
            'nomor_stock_opname' => '',
            'periode'            => '',
            'tgl_opname'         => '',
            'petugas_nama'       => '',
            'petugas_nip'        => '',
            'kepala_gudang_nama' => '',
            'kepala_gudang_nip'  => '',
            'mengetahui_nama'    => '',
            'mengetahui_nip'     => '',
        ];

        $isFiltered = $request->filled('gudang_id') && $request->filled('periode') && $request->filled('tgl_opname');

        if ($isFiltered) {
            $gudangId = $request->gudang_id;
            $periode = $request->periode;
            $tglOpname = $request->tgl_opname;

            // Fetch items matching category optional filter
            $itemQuery = Item::with(['satuanKecil', 'kategori']);
            if ($request->filled('kategori_id')) {
                $itemQuery->where('kategori_id', $request->kategori_id);
            }
            $items = $itemQuery->orderBy('nama_barang')->get();

            // Tempelkan stok sistem saat ini untuk masing-masing item di gudang tersebut
            foreach ($items as $item) {
                $item->stok_sistem_gudang = $item->getStockKecilInWarehouse($gudangId);
            }

            // Cari jika ada stock opnames yang sudah tersimpan sebelumnya
            $existingOpnames = StockOpname::where('gudang_id', $gudangId)
                ->where('periode', $periode)
                ->where('tgl_opname', $tglOpname)
                ->get()
                ->keyBy('barang_id');

            $firstOpname = $existingOpnames->first();
            if ($firstOpname) {
                $metadata = [
                    'nomor_stock_opname' => $firstOpname->nomor_stock_opname,
                    'periode'            => $firstOpname->periode,
                    'tgl_opname'         => $firstOpname->tgl_opname
                                            ? (is_string($firstOpname->tgl_opname)
                                                ? Carbon::parse($firstOpname->tgl_opname)->format('Y-m-d')
                                                : $firstOpname->tgl_opname->format('Y-m-d'))
                                            : $tglOpname,
                    'petugas_nama'       => $firstOpname->petugas_nama,
                    'petugas_nip'        => $firstOpname->petugas_nip,
                    'kepala_gudang_nama' => $firstOpname->kepala_gudang_nama,
                    'kepala_gudang_nip'  => $firstOpname->kepala_gudang_nip,
                    'mengetahui_nama'    => $firstOpname->mengetahui_nama,
                    'mengetahui_nip'     => $firstOpname->mengetahui_nip,
                ];
            } else {
                // Generate usulan nomor dokumen jika baru
                $metadata['nomor_stock_opname'] = 'SO/' . str_replace(' ', '-', $periode) . '/' . date('dmy');
                $metadata['periode'] = $periode;
                $metadata['tgl_opname'] = $tglOpname;
                
                // Cari default kepala pelaksana untuk Mengetahui
                $kepalaPelaksana = FirstParty::where('jabatan', 'like', '%Kepala Pelaksana%')->first();
                $metadata['mengetahui_nama'] = $kepalaPelaksana ? $kepalaPelaksana->nama_pihak : 'RONI, A.Ks., M.M';
                $metadata['mengetahui_nip'] = $kepalaPelaksana ? $kepalaPelaksana->nip : '19690901 199303 1 004';
            }
        }

        return view('pages.transaksi.stock_opname', compact(
            'warehouses', 'categories', 'firstParties', 'users',
            'historyOpnames', 'items', 'existingOpnames', 'metadata', 'isFiltered'
        ));
    }

    public function saveBatch(Request $request)
    {
        $request->validate([
            'gudang_id'          => 'required|exists:warehouses,id',
            'nomor_stock_opname' => 'required|string|max:100',
            'periode'            => 'required|string|max:50',
            'tgl_opname'         => 'required|date',
            'items'              => 'required|array',
            'items.*.barang_id'  => 'required|exists:items,id',
            'items.*.stok_sistem'=> 'required|integer|min:0',
            'items.*.stok_fisik' => 'required|integer|min:0',
            'items.*.kondisi_barang' => 'nullable|string|max:50',
            'items.*.keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            $this->opnameService->saveBatchOpname($request->all());
            return redirect()->route('stock-opname.index', [
                'gudang_id'          => $request->gudang_id,
                'periode'            => $request->periode,
                'tgl_opname'         => $request->tgl_opname,
                'kategori_id'        => $request->kategori_id,
            ])->with('success', 'Stock Opname berhasil disimpan dan stok sistem telah disesuaikan.');

        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan Stock Opname: ' . $e->getMessage());
        }
    }

    public function printReport(Request $request)
    {
        $request->validate([
            'gudang_id'          => 'required|exists:warehouses,id',
            'periode'            => 'required|string',
            'tgl_opname'         => 'required|date',
        ]);

        $gudang = Warehouse::findOrFail($request->gudang_id);
        $periode = $request->periode;
        $tglOpname = $request->tgl_opname;

        // Ambil data stock opname tersimpan
        $opnames = StockOpname::with(['barang.satuanKecil', 'gudang'])
            ->where('gudang_id', $gudang->id)
            ->where('periode', $periode)
            ->where('tgl_opname', $tglOpname);

        if ($request->filled('kategori_id')) {
            $opnames->whereHas('barang', function ($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        $opnames = $opnames->get();

        if ($opnames->isEmpty()) {
            return back()->with('error', 'Tidak ada data stock opname tersimpan untuk dicetak pada periode & gudang tersebut. Silakan isi dan simpan terlebih dahulu.');
        }

        $first = $opnames->first();
        $nomorSo = $first->nomor_stock_opname;
        
        // Sanitasi nama file: hapus karakter / \ : * ? " < > | yang tidak valid untuk nama file
        $safeFilename = preg_replace('/[\/\\\:\*\?"<>\|]/', '-', $nomorSo);
        $safeFilename = trim(preg_replace('/-+/', '-', $safeFilename), '-');
        
        $petugas = [
            'nama' => $first->petugas_nama ?? '......................................',
            'nip' => $first->petugas_nip ?? '-',
        ];
        $kepalaGudang = [
            'nama' => $first->kepala_gudang_nama ?? '......................................',
            'nip' => $first->kepala_gudang_nip ?? '-',
        ];
        $mengetahui = [
            'nama' => $first->mengetahui_nama ?? 'RONI, A.Ks., M.M',
            'nip' => $first->mengetahui_nip ?? '19690901 199303 1 004',
        ];

        // Format tanggal cetak terjemahan local
        $tglCetak = \Carbon\Carbon::parse($tglOpname)->translatedFormat('d F Y');

        $pdf = Pdf::loadView('reports.opname', compact(
            'opnames', 'gudang', 'periode', 'tglOpname', 'nomorSo', 'petugas', 'kepalaGudang', 'mengetahui', 'tglCetak'
        ))->setPaper('a4', 'landscape');

        if ($request->has('preview')) {
            return $pdf->stream('Laporan-Stock-Opname-' . $safeFilename . '.pdf');
        }

        return $pdf->download('Laporan-Stock-Opname-' . $safeFilename . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'gudang_id'          => 'required|exists:warehouses,id',
            'periode'            => 'required|string',
            'tgl_opname'         => 'required|date',
        ]);

        $filters = $request->only(['gudang_id', 'periode', 'tgl_opname', 'kategori_id']);
        
        $nomorSo = 'SO-' . preg_replace('/[\/\\\:\*\?"<>\|]/', '-', $filters['periode'] . '-' . date('dmy'));

        return Excel::download(new OpnameExport($filters), 'Laporan-Stock-Opname-' . $nomorSo . '.xlsx');
    }



    public function store(Request $request)
    {
        // Tetap pertahankan untuk backward compatibility jika ada service pemanggil
        $request->validate([
            'barang_id'  => 'required|exists:items,id',
            'gudang_id'  => 'required|exists:warehouses,id',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            $this->opnameService->processOpname($request->all());
            return redirect()->route('stock-opname.index')
                ->with('success', 'Stock Opname berhasil diproses.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memproses Stock Opname: ' . $e->getMessage());
        }
    }
}
