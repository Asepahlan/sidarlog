<?php

namespace App\Http\Controllers;

use App\Services\StockOpnameService;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    protected $opnameService;

    public function __construct(StockOpnameService $opnameService)
    {
        $this->opnameService = $opnameService;
    }

    public function index(Request $request)
    {
        $query = \App\Models\StockOpname::with(['barang', 'gudang', 'pengguna']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('month')) {
            $date = \Carbon\Carbon::parse($request->month);
            $query->whereMonth('created_at', $date->month)
                  ->whereYear('created_at', $date->year);
        }

        if ($request->filled('gudang_id')) {
            $query->where('gudang_id', $request->gudang_id);
        }

        $opnames = $query->latest()->paginate(15);
        $warehouses = \App\Models\Warehouse::all();

        return view('pages.transaksi.stock_opname', compact('opnames', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'    => 'required|exists:items,id',
            'gudang_id'    => 'required|exists:warehouses,id',
            'stok_fisik'   => 'required|integer|min:0',
            'keterangan'   => 'nullable|string|max:1000',
        ]);

        try {
            $this->opnameService->processOpname($request->all());
            return redirect()->route('stock-opname.index')
                ->with('success', 'Stock Opname berhasil diproses dan stok telah disesuaikan.');

        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Gagal memproses Stock Opname: ' . $e->getMessage());
        }
    }
}
