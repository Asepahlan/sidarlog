<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::latest()->get();
        return view('pages.master.gudang', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_gudang' => 'required|unique:warehouses',
            'nama_gudang' => 'required',
            'lokasi' => 'nullable'
        ]);

        $warehouse = Warehouse::create($request->all());
        ActivityLog::log("Menambah gudang: {$warehouse->nama_gudang}", "Master Gudang", $request->all());

        return redirect()->back()->with('success', 'Gudang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_gudang' => 'required',
            'lokasi' => 'nullable'
        ]);

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($request->all());
        ActivityLog::log("Memperbarui gudang: {$warehouse->nama_gudang}", "Master Gudang", $request->all());

        return redirect()->back()->with('success', 'Gudang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        ActivityLog::log("Menghapus gudang: {$warehouse->nama_gudang}", "Master Gudang");
        $warehouse->delete();

        return redirect()->back()->with('success', 'Gudang berhasil dihapus');
    }
}
