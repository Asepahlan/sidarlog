@extends('layouts.app')

@section('title', 'Buat Mutasi Gudang')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('mutasi-gudang.index') }}" class="w-10 h-10 bg-white dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-all">
            <i class="fas fa-arrow-left text-gray-500 text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Buat Mutasi Gudang</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Pindahkan stok barang dari satu gudang ke gudang lain.</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('mutasi-gudang.store') }}" method="POST" class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-8 space-y-6">
        @csrf

        {{-- Barang --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Barang yang Dimutasi</label>
            <select name="barang_id" required id="barang_id"
                class="w-full px-4 py-2.5 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">— Pilih Barang —</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-stok="{{ $item->stok_saat_ini_kecil }}">
                        [{{ $item->kode_barang }}] {{ $item->nama_barang }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Stok saat ini: <strong id="stok-info" class="text-primary-600">-</strong></p>
            @error('barang_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Gudang Asal & Tujuan --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-warehouse text-red-400 mr-1"></i> Gudang Asal
                </label>
                <select name="gudang_asal_id" required
                    class="w-full px-4 py-2.5 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">— Pilih Gudang Asal —</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->nama_gudang }}</option>
                    @endforeach
                </select>
                @error('gudang_asal_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-warehouse text-green-500 mr-1"></i> Gudang Tujuan
                </label>
                <select name="gudang_tujuan_id" required
                    class="w-full px-4 py-2.5 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">— Pilih Gudang Tujuan —</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->nama_gudang }}</option>
                    @endforeach
                </select>
                @error('gudang_tujuan_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Jumlah & Tanggal --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Jumlah yang Dimutasi</label>
                <input type="number" name="jumlah_barang_kecil" min="1" required
                    class="w-full px-4 py-2.5 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none"
                    placeholder="0">
                @error('jumlah_barang_kecil')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Tanggal Mutasi</label>
                <input type="date" name="tgl_mutasi" required value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-2.5 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                @error('tgl_mutasi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Keterangan --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Keterangan (Opsional)</label>
            <textarea name="keterangan" rows="3"
                class="w-full px-4 py-2.5 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none"
                placeholder="Alasan mutasi, catatan khusus, dll..."></textarea>
        </div>

        {{-- Info Box --}}
        <div class="bg-primary-50 dark:bg-navy-800 border border-primary-100 dark:border-navy-700 rounded-2xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-info-circle text-primary-500 mt-0.5"></i>
                <div>
                    <p class="text-sm font-bold text-primary-700 dark:text-primary-400">Catatan Penting</p>
                    <p class="text-xs text-primary-600 dark:text-primary-500 mt-1">
                        Mutasi akan otomatis mencatat <strong>Barang Keluar</strong> dari gudang asal dan <strong>Barang Masuk</strong> ke gudang tujuan. Nomor mutasi akan digenerate secara otomatis.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('mutasi-gudang.index') }}" class="px-6 py-2.5 text-gray-500 hover:text-gray-700 font-medium transition-colors">Batal</a>
            <button type="submit" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 transition-all">
                <i class="fas fa-shuffle mr-2"></i> Proses Mutasi
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('barang_id').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const stok = selected.dataset.stok;
        document.getElementById('stok-info').textContent = stok ? stok + ' unit' : '-';
    });
</script>
@endsection
