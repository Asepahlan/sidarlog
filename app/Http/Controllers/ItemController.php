<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ItemLocation;
use App\Models\BudgetSource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $kategoriId = $request->input('kategori_id');

        $items = $this->itemService->getAllItems($perPage, $search, $kategoriId);
        $categories = Category::latest()->get();

        return view('pages.barang.index', compact('items', 'categories', 'search', 'kategoriId'));
    }

    public function create()
    {
        return redirect()->route('barang.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_barang'      => 'nullable|string|max:50|unique:items,kode_barang',
            'nama_barang'      => 'required|string|max:255',
            'kategori_id'      => 'required|exists:categories,id',
            'satuan_kecil_id'  => 'required|exists:units,id',
            'satuan_besar_id'  => 'nullable|exists:units,id',
            'harga_satuan_kecil' => 'nullable|numeric|min:0',
            'harga_satuan_besar' => 'nullable|numeric|min:0',
            'sumber_anggaran_id' => 'nullable|exists:budget_sources,id',
            'lokasi_barang_id' => 'nullable|exists:item_locations,id',
            'stok_minimal'     => 'required|integer|min:0',
            'deskripsi'        => 'nullable|string',
            'tgl_kadaluarsa'   => 'nullable|date',
            'tgl_diterima'     => 'nullable|date',
        ]);

        try {
            $this->itemService->createItem($data);
            return redirect()->route('barang.index')
                ->with('success', 'Data barang berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $item = $this->itemService->getItemById($id);
        return view('pages.barang.show', compact('item'));
    }

    public function edit($id)
    {
        return redirect()->route('barang.index');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_barang'      => 'required|string|max:255',
            'kategori_id'      => 'required|exists:categories,id',
            'satuan_kecil_id'  => 'required|exists:units,id',
            'satuan_besar_id'  => 'nullable|exists:units,id',
            'harga_satuan_kecil' => 'nullable|numeric|min:0',
            'harga_satuan_besar' => 'nullable|numeric|min:0',
            'sumber_anggaran_id' => 'nullable|exists:budget_sources,id',
            'lokasi_barang_id' => 'nullable|exists:item_locations,id',
            'stok_minimal'     => 'required|integer|min:0',
            'deskripsi'        => 'nullable|string',
            'tgl_kadaluarsa'   => 'nullable|date',
            'tgl_diterima'     => 'nullable|date',
        ]);

        try {
            $this->itemService->updateItem($id, $data);
            return redirect()->route('barang.index')
                ->with('success', 'Data barang berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->itemService->deleteItem($id);
            return redirect()->route('barang.index')
                ->with('success', 'Data barang berhasil dipindahkan ke Trash.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function trash()
    {
        $items = $this->itemService->getTrashedItems();
        return view('pages.barang.trash', compact('items'));
    }

    public function restore($id)
    {
        try {
            $this->itemService->restoreItem($id);
            return redirect()->route('barang.trash')
                ->with('success', 'Data barang berhasil dikembalikan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengembalikan data: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->itemService->forceDeleteItem($id);
            return redirect()->route('barang.trash')
                ->with('success', 'Data barang berhasil dihapus permanen.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }

    public function generateQr($code)
    {
        // Cari barang berdasarkan kode unik
        $item = \App\Models\Item::where('kode_barang', $code)->first();
        
        // Gunakan URL detail barang jika ditemukan, jika tidak gunakan kode barang mentah
        $content = $item ? route('barang.show', $item->id) : $code;

        $qr = (string) QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->color(30, 41, 59) // Slate 800 (navy gelap premium) untuk kontras tinggi
            ->generate($content);

        return response($qr)->header('Content-Type', 'image/svg+xml');
    }
}
