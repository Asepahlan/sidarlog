<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ItemTypeController extends Controller
{
    public function index()
    {
        $types = ItemType::with('kategori')->latest()->get();
        $categories = Category::all();
        return view('pages.master.jenis_barang', compact('types', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required',
            'nama_jenis' => 'required'
        ]);

        $type = ItemType::create($request->all());
        ActivityLog::log("Menambah Jenis Barang: {$type->nama_jenis}", "Master Kategori", $request->all());
        return redirect()->back()->with('success', 'Jenis barang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_id' => 'required',
            'nama_jenis' => 'required'
        ]);

        $type = ItemType::findOrFail($id);
        $type->update($request->all());
        ActivityLog::log("Memperbarui Jenis Barang: {$type->nama_jenis}", "Master Kategori", $request->all());
        return redirect()->back()->with('success', 'Jenis barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $type = ItemType::findOrFail($id);
        ActivityLog::log("Menghapus Jenis Barang: {$type->nama_jenis}", "Master Kategori");
        $type->delete();
        return redirect()->back()->with('success', 'Jenis barang berhasil dihapus');
    }
}
