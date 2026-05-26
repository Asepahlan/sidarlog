@extends('layouts.app')

@section('title', 'Edit Barang: ' . $item->nama_barang)

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Data Barang</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Perbarui informasi untuk barang <strong>{{ $item->nama_barang }}</strong> ({{ $item->kode_barang }}).</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-8">
        <form action="{{ route('barang.update', $item->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kode Barang (Readonly)</label>
                    <input type="text" value="{{ $item->kode_barang }}" readonly class="w-full px-4 py-3 border rounded-xl bg-gray-50 dark:bg-navy-900/50 dark:border-gray-700 dark:text-gray-400 outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Barang</label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang', $item->nama_barang) }}" required class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                    <select name="kategori_id" required class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('kategori_id', $item->kategori_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Sumber Anggaran</label>
                    <select name="sumber_anggaran_id" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Sumber</option>
                        @foreach(\App\Models\BudgetSource::all() as $source)
                            <option value="{{ $source->id }}" {{ old('sumber_anggaran_id', $item->sumber_anggaran_id) == $source->id ? 'selected' : '' }}>{{ $source->nama_sumber }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-primary-600 uppercase tracking-wider">Satuan Kecil</h3>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Satuan</label>
                        <select name="satuan_kecil_id" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="">Pilih Satuan</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('satuan_kecil_id', $item->satuan_kecil_id) == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Harga (Rp)</label>
                        <input type="number" name="harga_satuan_kecil" value="{{ old('harga_satuan_kecil', $item->harga_satuan_kecil) }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                </div>
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-primary-600 uppercase tracking-wider">Satuan Besar</h3>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Satuan</label>
                        <select name="satuan_besar_id" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="">Pilih Satuan</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('satuan_besar_id', $item->satuan_besar_id) == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Harga (Rp)</label>
                        <input type="number" name="harga_satuan_besar" value="{{ old('harga_satuan_besar', $item->harga_satuan_besar) }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Lokasi Barang</label>
                    <select name="lokasi_barang_id" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Lokasi</option>
                        @foreach(\App\Models\ItemLocation::all() as $location)
                            <option value="{{ $location->id }}" {{ old('lokasi_barang_id', $item->lokasi_barang_id) == $location->id ? 'selected' : '' }}>{{ $location->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Stok Minimal (Warning)</label>
                    <input type="number" name="stok_minimal" value="{{ old('stok_minimal', $item->stok_minimal) }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Kadaluarsa</label>
                    <input type="date" name="tgl_kadaluarsa" value="{{ old('tgl_kadaluarsa', $item->tgl_kadaluarsa ? $item->tgl_kadaluarsa->format('Y-m-d') : '') }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Diterima</label>
                    <input type="date" name="tgl_diterima" value="{{ old('tgl_diterima', $item->tgl_diterima ? $item->tgl_diterima->format('Y-m-d') : '') }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Barang</label>
                <textarea name="deskripsi" rows="3" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Catatan atau deskripsi barang...">{{ old('deskripsi', $item->deskripsi) }}</textarea>
            </div>

            <div class="pt-6 flex justify-end space-x-3">
                <a href="{{ route('barang.index') }}" class="px-6 py-3 text-gray-500 hover:text-gray-700 font-bold">Batal</a>
                <button type="submit" class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 transition-all transform active:scale-95">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
