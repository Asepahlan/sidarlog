<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Exports\ItemsExport;
use App\Exports\TransaksiExport;
use App\Exports\OpnameExport;
use App\Exports\MutasiExport;
use App\Models\StockOpname;
use App\Models\StockMutation;
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

    public function exportItemsExcel()
    {
        return Excel::download(new ItemsExport, 'data-barang-' . date('Y-m-d') . '.xlsx');
    }

    public function exportItemsPdf()
    {
        $items = Item::with(['kategori', 'satuanKecil', 'satuanBesar'])->get();
        $pdf = Pdf::loadView('reports.items', compact('items'));
        return $pdf->download('data-barang-' . date('Y-m-d') . '.pdf');
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

    public function exportMutasiExcel(Request $request)
    {
        $filters = $request->only(['search', 'gudang_asal_id', 'gudang_tujuan_id', 'start_date', 'end_date']);
        return Excel::download(new MutasiExport($filters), 'laporan-mutasi-gudang-' . date('Y-m-d') . '.xlsx');
    }

    public function exportMutasiPdf(Request $request)
    {
        $query = StockMutation::with(['barang', 'gudangAsal', 'gudangTujuan', 'pembuat']);
        $filterParts = [];

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_mutasi', 'like', '%' . $search . '%')
                  ->orWhereHas('barang', function ($itemQuery) use ($search) {
                      $itemQuery->where('nama_barang', 'like', '%' . $search . '%')
                                ->orWhere('kode_barang', 'like', '%' . $search . '%');
                  });
            });
            $filterParts[] = 'Cari: "' . $search . '"';
        }

        if ($request->filled('gudang_asal_id')) {
            $query->where('gudang_asal_id', $request->gudang_asal_id);
            $gudang = \App\Models\Warehouse::find($request->gudang_asal_id);
            if ($gudang) {
                $filterParts[] = 'Asal: ' . $gudang->nama_gudang;
            }
        }

        if ($request->filled('gudang_tujuan_id')) {
            $query->where('gudang_tujuan_id', $request->gudang_tujuan_id);
            $gudang = \App\Models\Warehouse::find($request->gudang_tujuan_id);
            if ($gudang) {
                $filterParts[] = 'Tujuan: ' . $gudang->nama_gudang;
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_mutasi', '>=', $request->start_date);
            $filterParts[] = 'Mulai: ' . Carbon::parse($request->start_date)->format('d/m/Y');
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_mutasi', '<=', $request->end_date);
            $filterParts[] = 'Hingga: ' . Carbon::parse($request->end_date)->format('d/m/Y');
        }

        $mutations = $query->latest()->get();
        $filterInfo = implode(', ', $filterParts);

        $pdf = Pdf::loadView('reports.mutasi', compact('mutations', 'filterInfo'));
        return $pdf->download('laporan-mutasi-gudang-' . date('Y-m-d') . '.pdf');
    }
}
