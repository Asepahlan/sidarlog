<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Exports\ItemsExport;
use App\Exports\TransaksiExport;
use App\Exports\OpnameExport;
use App\Models\StockOpname;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('pages.laporan.index');
    }

    public function exportItemsExcel(Request $request)
    {
        $gudangId = $request->query('gudang_id');
        $month = $request->query('month', date('Y-m'));
        return Excel::download(new ItemsExport($gudangId, $month), 'daftar-persediaan-logistik-' . $month . '.xlsx');
    }

    public function exportItemsPdf(Request $request)
    {
        $gudangId = $request->query('gudang_id');
        $month = $request->query('month', date('Y-m'));
        
        $gudang = $gudangId ? \App\Models\Warehouse::find($gudangId) : null;
        $namaGudang = $gudang ? $gudang->nama_gudang : 'Semua Gudang';

        $parsedDate = Carbon::parse($month . '-01');
        $startDate = $parsedDate->copy()->startOfMonth();
        $endDate = $parsedDate->copy()->endOfMonth();
        
        $monthNameUpper = strtoupper($parsedDate->translatedFormat('F Y'));

        $items = Item::with(['kategori', 'satuanKecil', 'sumberAnggaran'])->get();

        foreach ($items as $item) {
            // 1. Saldo Awal (before start date of selected month)
            $item->saldo_awal = $this->calculateStockBefore($item->id, $gudangId, $startDate);
            
            // 2. Masuk (during selected month)
            $item->masuk = $this->calculateStockBetween($item->id, $gudangId, $startDate, $endDate, 'masuk');
            
            // 3. Keluar (during selected month)
            $item->keluar = $this->calculateStockBetween($item->id, $gudangId, $startDate, $endDate, 'keluar');
            
            // 4. Penyesuaian (during selected month)
            $item->penyesuaian = $this->calculateStockBetween($item->id, $gudangId, $startDate, $endDate, 'penyesuaian');

            // Hitung sisa akhir (Saldo Awal + Masuk - Keluar + Penyesuaian)
            $item->sisa_akhir = $item->saldo_awal + $item->masuk - $item->keluar + $item->penyesuaian;

            // Hitung nilai rupiah nominal
            $harga = $item->harga_satuan_kecil ?? 0;
            $item->saldo_awal_val = $item->saldo_awal * $harga;
            
            // Masuk value (including positive adjustment)
            $masukQty = $item->masuk + ($item->penyesuaian > 0 ? $item->penyesuaian : 0);
            $item->masuk_val = $masukQty * $harga;
            
            // Keluar value (including negative adjustment)
            $keluarQty = $item->keluar + ($item->penyesuaian < 0 ? abs($item->penyesuaian) : 0);
            $item->keluar_val = $keluarQty * $harga;
            
            $item->sisa_akhir_val = $item->sisa_akhir * $harga;
        }

        // Tanda tangan pejabat dari database
        $kabid = \App\Models\FirstParty::where('jabatan', 'like', '%Kepala Bidang%')->first();
        $pengelola = \App\Models\FirstParty::where('jabatan', 'like', '%Pengelola%')->first() 
            ?? \App\Models\FirstParty::where('nama_pihak', 'like', '%UMAN%')->first();

        $kabidNama = $kabid ? $kabid->nama_pihak : 'CAHYONO RAHMAN, S.T';
        $kabidNip = $kabid ? $kabid->nip : '19720130 200501 1 001';
        $pengelolaNama = $pengelola ? $pengelola->nama_pihak : 'UMAN SUHERMAN, S.IP';
        $pengelolaNip = $pengelola ? $pengelola->nip : '19730713 200701 1 005';

        $totalSisaAkhirVal = $items->sum('sisa_akhir_val');
        $terbilangText = $this->terbilang($totalSisaAkhirVal);

        $tglCetak = Carbon::now()->translatedFormat('F Y');

        $pdf = Pdf::loadView('reports.items', compact(
            'items', 'namaGudang', 'monthNameUpper', 'kabidNama', 'kabidNip', 
            'pengelolaNama', 'pengelolaNip', 'terbilangText', 'tglCetak'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('daftar-persediaan-logistik-' . $month . '.pdf');
    }

    private function calculateStockBefore($itemId, $gudangId, $date)
    {
        $queryIn = StockTransaction::where('barang_id', $itemId)
            ->where('tgl_transaksi', '<', $date)
            ->where('jenis', 'masuk');

        $queryOut = StockTransaction::where('barang_id', $itemId)
            ->where('tgl_transaksi', '<', $date)
            ->where('jenis', 'keluar');

        $queryAdj = StockTransaction::where('barang_id', $itemId)
            ->where('tgl_transaksi', '<', $date)
            ->where('jenis', 'penyesuaian');

        if ($gudangId) {
            $queryIn->where('gudang_id', $gudangId);
            $queryOut->where('gudang_id', $gudangId);
            $queryAdj->where('gudang_id', $gudangId);
        }

        return (int)$queryIn->sum('jumlah_barang_kecil') 
            - (int)$queryOut->sum('jumlah_barang_kecil') 
            + (int)$queryAdj->sum('jumlah_barang_kecil');
    }

    private function calculateStockBetween($itemId, $gudangId, $startDate, $endDate, $jenis)
    {
        $query = StockTransaction::where('barang_id', $itemId)
            ->whereBetween('tgl_transaksi', [$startDate, $endDate])
            ->where('jenis', $jenis);

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        return (int) $query->sum('jumlah_barang_kecil');
    }

    private function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut($nilai % 1000000000);
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut($nilai % 1000000000000);
        }     
        return $temp;
    }

    private function terbilang($nilai) {
        if ($nilai <= 0) {
            return "Nol Rupiah";
        }
        $hasil = trim($this->penyebut($nilai));
        return ucwords($hasil) . " Rupiah";
    }

    public function exportTransactionsExcel(Request $request)
    {
        $jenis   = $request->query('jenis', 'semua');
        $filters = $request->only(['start_date', 'end_date']);
        $label   = in_array($jenis, ['masuk', 'keluar']) ? $jenis : 'semua';
        return Excel::download(new TransaksiExport($jenis, $filters), 'laporan-transaksi-' . $label . '-' . date('Y-m-d') . '.xlsx');
    }

    public function exportTransactionsPdf(Request $request)
    {
        $jenis = $request->query('jenis');
        $filterParts = [];

        $query = StockTransaction::with(['barang', 'gudang', 'pengguna', 'pihakKesatu', 'pihakKedua', 'referenceBap']);

        if ($jenis) {
            $query->where('jenis', $jenis);
            $filterParts[] = 'Jenis: ' . ucfirst($jenis);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_transaksi', '>=', $request->start_date);
            $filterParts[] = 'Mulai: ' . Carbon::parse($request->start_date)->format('d/m/Y');
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_transaksi', '<=', $request->end_date);
            $filterParts[] = 'Hingga: ' . Carbon::parse($request->end_date)->format('d/m/Y');
        }

        $transactions = $query->orderBy('tgl_transaksi', 'desc')->get();
        $filterInfo   = implode(', ', $filterParts);

        $pdf = Pdf::loadView('reports.transactions', compact('transactions', 'jenis', 'filterInfo'));
        return $pdf->download('laporan-transaksi-' . ($jenis ?? 'semua') . '-' . date('Y-m-d') . '.pdf');
    }

    public function exportOpnameExcel(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'month', 'gudang_id']);
        return Excel::download(new OpnameExport($filters), 'laporan-stock-opname-' . date('Y-m-d') . '.xlsx');
    }

    public function exportOpnamePdf(Request $request)
    {
        $query = StockOpname::with(['barang', 'gudang', 'pengguna']);
        $filterParts = [];

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
            $filterParts[] = 'Mulai: ' . Carbon::parse($request->start_date)->format('d/m/Y');
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
            $filterParts[] = 'Hingga: ' . Carbon::parse($request->end_date)->format('d/m/Y');
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('created_at', $date->month)
                  ->whereYear('created_at', $date->year);
            $filterParts[] = 'Bulan: ' . $date->translatedFormat('F Y');
        }

        if ($request->filled('gudang_id')) {
            $query->where('gudang_id', $request->gudang_id);
            $gudang = \App\Models\Warehouse::find($request->gudang_id);
            if ($gudang) {
                $filterParts[] = 'Gudang: ' . $gudang->nama_gudang;
            }
        }

        $opnames = $query->latest()->get();
        $filterInfo = implode(', ', $filterParts);

        $pdf = Pdf::loadView('reports.opname', compact('opnames', 'filterInfo'));
        return $pdf->download('laporan-stock-opname-' . date('Y-m-d') . '.pdf');
    }
}
