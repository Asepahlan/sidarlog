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

    public function index()
    {
        $opnames = $this->opnameService->getAllOpnames();
        return view('pages.transaksi.stock_opname', compact('opnames'));
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
