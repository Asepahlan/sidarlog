<?php

namespace App\Http\Controllers;

use App\Models\ReferenceBap;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ReferenceBapController extends Controller
{
    public function index()
    {
        $baps = ReferenceBap::latest()->get();
        return view('pages.master.bap', compact('baps'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_ba' => 'required|unique:reference_baps',
            'judul_ba' => 'required',
            'tgl_ba' => 'required|date'
        ]);

        $bap = ReferenceBap::create($request->all());
        ActivityLog::log("Menambah Referensi BAP: {$bap->nomor_ba}", "Master Data", $request->all());
        return redirect()->back()->with('success', 'Referensi BAP berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nomor_ba' => 'required|unique:reference_baps,nomor_ba,'.$id,
            'judul_ba' => 'required',
            'tgl_ba' => 'required|date'
        ]);

        $bap = ReferenceBap::findOrFail($id);
        $bap->update($request->all());
        ActivityLog::log("Memperbarui Referensi BAP: {$bap->nomor_ba}", "Master Data", $request->all());
        return redirect()->back()->with('success', 'Referensi BAP berhasil diperbarui');
    }

    public function destroy($id)
    {
        $bap = ReferenceBap::findOrFail($id);
        ActivityLog::log("Menghapus Referensi BAP: {$bap->nomor_ba}", "Master Data");
        $bap->delete();
        return redirect()->back()->with('success', 'Referensi BAP berhasil dihapus');
    }
}
