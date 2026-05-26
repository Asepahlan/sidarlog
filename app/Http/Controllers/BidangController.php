<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    public function index()
    {
        $bidangs = Bidang::latest()->get();
        return view('pages.sistem.bidang', compact('bidangs'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_bidang' => 'required']);
        $bidang = Bidang::create($request->all());
        ActivityLog::log("Menambah bidang: {$bidang->nama_bidang}", "Sistem", $request->all());
        return redirect()->back()->with('success', 'Bidang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_bidang' => 'required']);
        $bidang = Bidang::findOrFail($id);
        $bidang->update($request->all());
        ActivityLog::log("Memperbarui bidang: {$bidang->nama_bidang}", "Sistem", $request->all());
        return redirect()->back()->with('success', 'Bidang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $bidang = Bidang::findOrFail($id);
        ActivityLog::log("Menghapus bidang: {$bidang->nama_bidang}", "Sistem");
        $bidang->delete();
        return redirect()->back()->with('success', 'Bidang berhasil dihapus');
    }
}
