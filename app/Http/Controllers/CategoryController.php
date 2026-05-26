<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('pages.master.kategori', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori',
            'deskripsi'     => 'nullable|string',
        ]);
        try {
            $category = Category::create($request->only(['nama_kategori', 'deskripsi']));
            ActivityLog::log("Menambah kategori: {$category->nama_kategori}", "Master Kategori", $request->all());
            return back()->with('success', 'Data kategori berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $id,
            'deskripsi'     => 'nullable|string',
        ]);
        try {
            $category = Category::findOrFail($id);
            $category->update($request->only(['nama_kategori', 'deskripsi']));
            ActivityLog::log("Memperbarui kategori: {$category->nama_kategori}", "Master Kategori", $request->all());
            return back()->with('success', 'Data kategori berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            ActivityLog::log("Menghapus kategori: {$category->nama_kategori}", "Master Kategori");
            $category->delete();
            return back()->with('success', 'Data kategori berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
