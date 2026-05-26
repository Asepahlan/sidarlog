<?php

namespace App\Http\Controllers;

use App\Models\ItemClassification;
use App\Models\ItemType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ItemClassificationController extends Controller
{
    public function index()
    {
        $classifications = ItemClassification::with('jenisBarang.kategori')->latest()->get();
        $types = ItemType::all();
        return view('pages.master.klasifikasi_barang', compact('classifications', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_barang_id' => 'required',
            'nama_klasifikasi' => 'required'
        ]);

        $classification = ItemClassification::create($request->all());
        ActivityLog::log("Menambah Klasifikasi Barang: {$classification->nama_klasifikasi}", "Master Kategori", $request->all());
        return redirect()->back()->with('success', 'Klasifikasi barang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_barang_id' => 'required',
            'nama_klasifikasi' => 'required'
        ]);

        $classification = ItemClassification::findOrFail($id);
        $classification->update($request->all());
        ActivityLog::log("Memperbarui Klasifikasi Barang: {$classification->nama_klasifikasi}", "Master Kategori", $request->all());
        return redirect()->back()->with('success', 'Klasifikasi barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $classification = ItemClassification::findOrFail($id);
        ActivityLog::log("Menghapus Klasifikasi Barang: {$classification->nama_klasifikasi}", "Master Kategori");
        $classification->delete();
        return redirect()->back()->with('success', 'Klasifikasi barang berhasil dihapus');
    }
}
