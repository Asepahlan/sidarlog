<?php

namespace App\Http\Controllers;

use App\Models\BudgetSource;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class BudgetSourceController extends Controller
{
    public function index()
    {
        $sources = BudgetSource::latest()->get();
        return view('pages.master.sumber_anggaran', compact('sources'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sumber'     => 'required|string|max:255',
            'tahun_anggaran'  => 'nullable|digits:4|integer',
            'deskripsi'       => 'nullable|string',
        ]);
        try {
            $source = BudgetSource::create($request->only(['nama_sumber', 'tahun_anggaran', 'deskripsi']));
            ActivityLog::log("Menambah sumber anggaran: {$source->nama_sumber}", "Master Data", $request->all());
            return back()->with('success', 'Data sumber anggaran berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sumber'     => 'required|string|max:255',
            'tahun_anggaran'  => 'nullable|digits:4|integer',
            'deskripsi'       => 'nullable|string',
        ]);
        try {
            $source = BudgetSource::findOrFail($id);
            $source->update($request->only(['nama_sumber', 'tahun_anggaran', 'deskripsi']));
            ActivityLog::log("Memperbarui sumber anggaran: {$source->nama_sumber}", "Master Data", $request->all());
            return back()->with('success', 'Data sumber anggaran berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $source = BudgetSource::findOrFail($id);
            ActivityLog::log("Menghapus sumber anggaran: {$source->nama_sumber}", "Master Data");
            $source->delete();
            return back()->with('success', 'Data sumber anggaran berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
