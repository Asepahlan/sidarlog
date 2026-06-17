<?php

namespace App\Http\Controllers;

use App\Models\SecondParty;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SecondPartyController extends Controller
{
    public function index()
    {
        $parties = SecondParty::latest()->get();
        return view('pages.master.pihak_kedua', compact('parties'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_pihak' => 'required']);
        $party = SecondParty::create($request->all());
        ActivityLog::log("Menambah Pihak Kedua: {$party->nama_pihak}", "Master Data", $request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $party,
                'message' => 'Data Pihak Kedua berhasil ditambahkan'
            ]);
        }

        return redirect()->back()->with('success', 'Data Pihak Kedua berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_pihak' => 'required']);
        $party = SecondParty::findOrFail($id);
        $party->update($request->all());
        ActivityLog::log("Memperbarui Pihak Kedua: {$party->nama_pihak}", "Master Data", $request->all());
        return redirect()->back()->with('success', 'Data Pihak Kedua berhasil diperbarui');
    }

    public function destroy($id)
    {
        $party = SecondParty::findOrFail($id);
        ActivityLog::log("Menghapus Pihak Kedua: {$party->nama_pihak}", "Master Data");
        $party->delete();
        return redirect()->back()->with('success', 'Data Pihak Kedua berhasil dihapus');
    }
}
