<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->get();
        return view('pages.master.satuan', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:100|unique:units,nama_satuan',
            'simbol'      => 'nullable|string|max:20',
        ]);
        try {
            $unit = Unit::create($request->only(['nama_satuan', 'simbol']));
            ActivityLog::log("Menambah satuan: {$unit->nama_satuan}", "Master Satuan", $request->all());
            return back()->with('success', 'Data satuan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:100|unique:units,nama_satuan,' . $id,
            'simbol'      => 'nullable|string|max:20',
        ]);
        try {
            $unit = Unit::findOrFail($id);
            $unit->update($request->only(['nama_satuan', 'simbol']));
            ActivityLog::log("Memperbarui satuan: {$unit->nama_satuan}", "Master Satuan", $request->all());
            return back()->with('success', 'Data satuan berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            ActivityLog::log("Menghapus satuan: {$unit->nama_satuan}", "Master Satuan");
            $unit->delete();
            return back()->with('success', 'Data satuan berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
