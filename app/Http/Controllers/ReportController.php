<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Exports\ItemsExport;
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

    public function exportTransactionsPdf(Request $request)
    {
        $jenis = $request->query('jenis');
        $transactions = StockTransaction::with(['barang', 'gudang', 'pengguna'])
            ->when($jenis, function ($query, $jenis) {
                return $query->where('jenis', $jenis);
            })
            ->get();
            
        $pdf = Pdf::loadView('reports.transactions', compact('transactions', 'jenis'));
        return $pdf->download('laporan-transaksi-' . ($jenis ?? 'semua') . '-' . date('Y-m-d') . '.pdf');
    }
}
