<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::latest()->get();
        return view('pages.sistem.jabatan', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_jabatan' => 'required']);
        $jabatan = Jabatan::create($request->all());
        ActivityLog::log("Menambah jabatan: {$jabatan->nama_jabatan}", "Sistem", $request->all());
        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_jabatan' => 'required']);
        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update($request->all());
        ActivityLog::log("Memperbarui jabatan: {$jabatan->nama_jabatan}", "Sistem", $request->all());
        return redirect()->back()->with('success', 'Jabatan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        ActivityLog::log("Menghapus jabatan: {$jabatan->nama_jabatan}", "Sistem");
        $jabatan->delete();
        return redirect()->back()->with('success', 'Jabatan berhasil dihapus');
    }
}
