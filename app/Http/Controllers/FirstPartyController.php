<?php

namespace App\Http\Controllers;

use App\Models\FirstParty;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class FirstPartyController extends Controller
{
    public function index()
    {
        $parties = FirstParty::latest()->get();
        return view('pages.master.pihak_pertama', compact('parties'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_pihak' => 'required']);
        $party = FirstParty::create($request->all());
        ActivityLog::log("Menambah Pihak Kesatu: {$party->nama_pihak}", "Master Data", $request->all());
        return redirect()->back()->with('success', 'Data Pihak Kesatu berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_pihak' => 'required']);
        $party = FirstParty::findOrFail($id);
        $party->update($request->all());
        ActivityLog::log("Memperbarui Pihak Kesatu: {$party->nama_pihak}", "Master Data", $request->all());
        return redirect()->back()->with('success', 'Data Pihak Kesatu berhasil diperbarui');
    }

    public function destroy($id)
    {
        $party = FirstParty::findOrFail($id);
        ActivityLog::log("Menghapus Pihak Kesatu: {$party->nama_pihak}", "Master Data");
        $party->delete();
        return redirect()->back()->with('success', 'Data Pihak Kesatu berhasil dihapus');
    }
}
