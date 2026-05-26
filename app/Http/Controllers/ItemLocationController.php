<?php

namespace App\Http\Controllers;

use App\Models\ItemLocation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ItemLocationController extends Controller
{
    public function index()
    {
        $locations = ItemLocation::latest()->get();
        return view('pages.master.lokasi_barang', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
        ]);
        try {
            $location = ItemLocation::create($request->only(['nama_lokasi', 'deskripsi']));
            ActivityLog::log("Menambah lokasi barang: {$location->nama_lokasi}", "Master Data", $request->all());
            return back()->with('success', 'Data lokasi berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
        ]);
        try {
            $location = ItemLocation::findOrFail($id);
            $location->update($request->only(['nama_lokasi', 'deskripsi']));
            ActivityLog::log("Memperbarui lokasi barang: {$location->nama_lokasi}", "Master Data", $request->all());
            return back()->with('success', 'Data lokasi berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $location = ItemLocation::findOrFail($id);
            ActivityLog::log("Menghapus lokasi barang: {$location->nama_lokasi}", "Master Data");
            $location->delete();
            return back()->with('success', 'Data lokasi berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
