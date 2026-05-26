@extends('layouts.app')

@section('title', 'Tambah Transaksi ' . ucfirst($jenis))

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Tambah Transaksi {{ ucfirst($jenis) }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Input data mutasi barang {{ $jenis }} ke sistem.</p>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
        <strong class="font-bold">Gagal menyimpan!</strong>
        <ul class="mt-1 list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-8">
        <form action="{{ route('barang-' . $jenis . '.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="jenis" value="{{ $jenis }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Barang</label>
                    <select name="barang_id" required class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Barang</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->kode_barang }} - {{ $item->nama_barang }} 
                                (Stok: {{ $item->current_stock_kecil }} {{ $item->satuanKecil->nama_satuan ?? 'Unit' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Gudang</label>
                    <select name="gudang_id" required class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('gudang_id') == $wh->id ? 'selected' : '' }}>{{ $wh->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jumlah (Kecil)</label>
                    <input type="number" name="jumlah_barang_kecil" value="{{ old('jumlah_barang_kecil', 0) }}" min="0" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jumlah (Besar)</label>
                    <input type="number" name="jumlah_barang_besar" value="{{ old('jumlah_barang_besar', 0) }}" min="0" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Transaksi</label>
                    <input type="datetime-local" name="tgl_transaksi" value="{{ old('tgl_transaksi', now()->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>

            @if($jenis == 'keluar')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pihak Kesatu (BPBD)</label>
                    <select name="pihak_kesatu_id" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Pihak Pertama</option>
                        @foreach($firstParties as $party)
                            <option value="{{ $party->id }}" {{ old('pihak_kesatu_id') == $party->id ? 'selected' : '' }}>{{ $party->nama_pihak }} - {{ $party->jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pihak Kedua (Penerima)</label>
                    <select name="pihak_kedua_id" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Pihak Kedua</option>
                        @foreach($secondParties as $party)
                            <option value="{{ $party->id }}" {{ old('pihak_kedua_id') == $party->id ? 'selected' : '' }}>{{ $party->nama_pihak }} - {{ $party->instansi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ $jenis == 'masuk' ? 'Nama Pengirim' : 'Nama Penerima (Lainnya)' }}</label>
                <input type="text" name="penerima_penyerah" value="{{ old('penerima_penyerah') }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Masukkan nama personil/pihak terkait">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Catatan tambahan untuk transaksi ini...">{{ old('keterangan') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-3">
                <a href="{{ route('barang-' . $jenis . '.index') }}" class="px-6 py-3 text-gray-500 hover:text-gray-700 font-bold transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 transition-all transform active:scale-95">
                    <i class="fas fa-save mr-2"></i> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
