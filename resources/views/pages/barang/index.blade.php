@extends('layouts.app')

@section('title', 'Master Barang')

@section('content')
<div class="space-y-6" x-data="{ 
    openCreate: {{ $errors->any() && !request()->has('_method') ? 'true' : 'false' }}, 
    openEdit: {{ $errors->any() && request()->has('_method') ? 'true' : 'false' }}, 
    openQr: false, 
    editItem: @if(old('id')) {
        id: '{{ old('id') }}',
        nama_barang: '{{ old('nama_barang') }}',
        kategori_id: '{{ old('kategori_id') }}',
        lokasi_barang_id: '{{ old('lokasi_barang_id') }}',
        sumber_anggaran_id: '{{ old('sumber_anggaran_id') }}',
        satuan_kecil_id: '{{ old('satuan_kecil_id') }}',
        satuan_besar_id: '{{ old('satuan_besar_id') }}',
        harga_satuan_kecil: '{{ old('harga_satuan_kecil') }}',
        harga_satuan_besar: '{{ old('harga_satuan_besar') }}',
        stok_minimal: '{{ old('stok_minimal') }}',
        deskripsi: '{{ old('deskripsi') }}',
        tgl_diterima: '{{ old('tgl_diterima') }}',
        tgl_kadaluarsa: '{{ old('tgl_kadaluarsa') }}'
    } @else {} @endif
}">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Master Barang</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola daftar inventaris barang, stok, dan klasifikasi sistem.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('laporan.barang.excel') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-500/20">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </a>
            <a href="{{ route('laporan.barang.pdf') }}" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-rose-500/20">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>
            @can('barang.delete')
            <a href="{{ route('barang.trash') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all">
                <i class="fas fa-trash-arrow-up mr-2"></i> Lihat Trash
            </a>
            @endcan
            @can('barang.create')
            <button @click="openCreate = true" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/30">
                <i class="fas fa-plus mr-2"></i> Tambah Barang
            </button>
            @endcan
        </div>
    </div>


    @if(session('error'))
    <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl flex items-center gap-3" role="alert">
        <i class="fas fa-exclamation-circle text-lg"></i>
        <span class="block sm:inline font-medium">{{ session('error') }}</span>
    </div>
    @endif
    @if(session('success'))
    <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3" role="alert">
        <i class="fas fa-check-circle text-lg"></i>
        <span class="block sm:inline font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Table Section -->
    <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <!-- Search & Filter Bar -->
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-navy-800/20">
            <form action="{{ route('barang.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode barang..." class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select name="kategori_id" class="w-full px-3 py-2.5 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $kategoriId == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex items-center space-x-2">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                            <i class="fas fa-filter mr-1.5"></i> Filter
                        </button>
                        @if($search || $kategoriId)
                            <a href="{{ route('barang.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-all">
                                <i class="fas fa-sync-alt mr-1.5"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-navy-800/50">
                    <tr>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-center w-12">No</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Barang</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Stok Saat Ini</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Harga Satuan</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Sumber & Lokasi</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Tanggal</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-4 py-3 text-[11px] text-gray-500 text-center">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('barang.show', $item->id) }}" class="flex flex-col group">
                                <span class="text-[11px] font-bold text-gray-900 dark:text-white leading-tight group-hover:text-primary-600 transition-colors">{{ $item->nama_barang }}</span>
                                <span class="text-[9px] text-gray-500 font-mono mt-0.5">{{ $item->kode_barang }}</span>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-[11px] text-gray-900 dark:text-white">
                            <div class="flex flex-col space-y-0.5">
                                <span class="font-medium text-emerald-600 dark:text-emerald-400">
                                    {{ $item->stok_saat_ini_kecil ?? 0 }} <span class="text-[9px] text-gray-500 font-normal">{{ optional($item->satuanKecil)->nama_satuan ?? 'pcs' }}</span>
                                </span>
                                @if($item->satuanBesar)
                                <span class="text-purple-600 dark:text-purple-400 font-medium">
                                    {{ $item->stok_saat_ini_besar ?? 0 }} <span class="text-[9px] text-gray-500 font-normal">{{ $item->satuanBesar->nama_satuan }}</span>
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-[11px] text-gray-900 dark:text-white">
                            <div class="flex flex-col space-y-0.5">
                                <span>Rp {{ number_format($item->harga_satuan_kecil ?? 0, 0, ',', '.') }} <span class="text-[9px] text-gray-500 font-normal">/ {{ optional($item->satuanKecil)->nama_satuan ?? 'pcs' }}</span></span>
                                @if($item->satuanBesar)
                                <span class="text-gray-500">Rp {{ number_format($item->harga_satuan_besar ?? 0, 0, ',', '.') }} <span class="text-[9px] text-gray-400 font-normal">/ {{ $item->satuanBesar->nama_satuan }}</span></span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-[11px] text-gray-900 dark:text-white">
                            <div class="flex flex-col space-y-0.5">
                                <span class="font-medium whitespace-nowrap"><i class="fas fa-wallet mr-1 text-gray-400 text-[9px]"></i> {{ optional($item->sumberAnggaran)->nama_sumber ?? '-' }}</span>
                                <span class="text-gray-500 whitespace-nowrap"><i class="fas fa-map-marker-alt mr-1 text-gray-400 text-[9px]"></i> {{ optional($item->lokasiBarang)->nama_lokasi ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-[11px] text-gray-900 dark:text-white">
                            <div class="flex flex-col space-y-0.5">
                                <span class="text-gray-500 whitespace-nowrap">Terima: {{ $item->tgl_diterima ? $item->tgl_diterima->format('d/m/y') : '-' }}</span>
                                <span class="whitespace-nowrap">
                                    Exp: 
                                    @if($item->tgl_kadaluarsa)
                                        <span class="{{ $item->tgl_kadaluarsa < \Carbon\Carbon::today() ? 'text-red-600 font-bold' : ($item->tgl_kadaluarsa <= \Carbon\Carbon::today()->addDays(30) ? 'text-yellow-600 font-bold' : 'text-gray-500') }}">
                                            {{ $item->tgl_kadaluarsa->format('d/m/y') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
                            <a href="{{ route('barang.show', $item->id) }}" class="text-gray-400 hover:text-primary-600 transition-colors" title="Detail Barang">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <button @click="editItem = JSON.parse($el.dataset.item); openQr = true" data-item="{{ json_encode(['id'=>$item->id,'kode_barang'=>$item->kode_barang,'nama_barang'=>$item->nama_barang]) }}" class="text-gray-400 hover:text-primary-600 transition-colors" title="QR Label">
                                <i class="fas fa-qrcode text-xs"></i>
                            </button>
                            @can('barang.edit')
                            <button @click="editItem = JSON.parse($el.dataset.item); openEdit = true" data-item="{{ json_encode(['id'=>$item->id,'nama_barang'=>$item->nama_barang,'kategori_id'=>$item->kategori_id,'lokasi_barang_id'=>$item->lokasi_barang_id,'sumber_anggaran_id'=>$item->sumber_anggaran_id,'satuan_kecil_id'=>$item->satuan_kecil_id,'satuan_besar_id'=>$item->satuan_besar_id,'harga_satuan_kecil'=>$item->harga_satuan_kecil,'harga_satuan_besar'=>$item->harga_satuan_besar,'stok_minimal'=>$item->stok_minimal,'deskripsi'=>$item->deskripsi,'tgl_diterima'=>$item->tgl_diterima?$item->tgl_diterima->format('Y-m-d'):null,'tgl_kadaluarsa'=>$item->tgl_kadaluarsa?$item->tgl_kadaluarsa->format('Y-m-d'):null]) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            @endcan
                            @can('barang.delete')
                            <form id="delete-form-{{ $item->id }}" action="{{ route('barang.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('delete-form-{{ $item->id }}', 'Hapus Barang?', 'Apakah Anda yakin ingin menghapus barang ' + '{{ $item->nama_barang }}' + '? Seluruh riwayat stok barang ini juga akan ikut terhapus.')" class="text-red-600 hover:text-red-800 transition-colors" title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Data barang belum tersedia.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div class="mt-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Tampilkan</span>
            <select onchange="window.location.href='?per_page='+this.value" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-navy-900 text-gray-700 dark:text-gray-300 rounded-lg text-sm px-2 py-1 outline-none">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="text-sm text-gray-500">data</span>
        </div>
        <div class="pagination-wrapper">
            {{ $items->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Modal Create -->
    <x-modal title="Tambah Barang Baru" x-show="openCreate">
        <form action="{{ route('barang.store') }}" method="POST" class="space-y-4 p-4" x-data="{ showAdvanced: false }">
            @csrf
            @if($errors->any() && !request()->has('_method'))
                <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-5">
                <!-- Core Fields (Quick Add) -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}" {{ old('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Satuan Kecil <span class="text-red-500">*</span></label>
                        <select name="satuan_kecil_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                            <option value="">Pilih Satuan</option>
                            @foreach(\App\Models\Unit::all() as $unit)
                                <option value="{{ $unit->id }}" {{ old('satuan_kecil_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Stok Minimal <span class="text-red-500">*</span></label>
                        <input type="number" name="stok_minimal" value="{{ old('stok_minimal', 0) }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                </div>

                <!-- Toggle Advanced Options -->
                <div class="border-t pt-4 dark:border-gray-700">
                    <button type="button" @click="showAdvanced = !showAdvanced" class="flex items-center text-xs font-bold text-primary-600 hover:text-primary-700 outline-none">
                        <span x-text="showAdvanced ? 'Sembunyikan Opsi Lanjutan' : 'Tampilkan Opsi Lanjutan (Lokasi, Satuan Besar, Anggaran, dll.)'"></span>
                        <i class="fas ml-1.5" :class="showAdvanced ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>

                <!-- Advanced Options Block -->
                <div x-show="showAdvanced" x-transition class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                Kode Barang
                                <span class="text-xs font-normal text-gray-400 ml-1">(Otomatis jika kosong)</span>
                            </label>
                            <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none" placeholder="Contoh: BRG-001">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Lokasi Barang</label>
                            <select name="lokasi_barang_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                                <option value="">Pilih Lokasi</option>
                                @foreach(\App\Models\ItemLocation::all() as $loc)
                                    <option value="{{ $loc->id }}" {{ old('lokasi_barang_id') == $loc->id ? 'selected' : '' }}>{{ $loc->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t pt-4 dark:border-navy-700">
                        <div class="space-y-4">
                            <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Harga Satuan Kecil</p>
                            <div class="flex flex-col gap-1.5">
                                <label class="block text-xs font-medium text-gray-500">Harga (Rp)</label>
                                <input type="number" name="harga_satuan_kecil" value="{{ old('harga_satuan_kecil') }}" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                            </div>
                        </div>
                        <div class="space-y-4">
                            <p class="text-xs font-bold text-purple-600 uppercase tracking-widest">Konfigurasi Satuan Besar</p>
                            <div class="flex flex-col gap-1.5">
                                <label class="block text-xs font-medium text-gray-500">Satuan</label>
                                <select name="satuan_besar_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                                    <option value="">— Tidak Ada —</option>
                                    @foreach(\App\Models\Unit::all() as $unit)
                                        <option value="{{ $unit->id }}" {{ old('satuan_besar_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="block text-xs font-medium text-gray-500">Harga (Rp)</label>
                                <input type="number" name="harga_satuan_besar" value="{{ old('harga_satuan_besar') }}" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Sumber Anggaran</label>
                            <select name="sumber_anggaran_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                                <option value="">Pilih Sumber</option>
                                @foreach(\App\Models\BudgetSource::all() as $source)
                                    <option value="{{ $source->id }}" {{ old('sumber_anggaran_id') == $source->id ? 'selected' : '' }}>{{ $source->nama_sumber }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea name="deskripsi" rows="2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none" placeholder="Keterangan tambahan...">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tgl Diterima</label>
                            <input type="date" name="tgl_diterima" value="{{ old('tgl_diterima', now()->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tgl Kadaluarsa</label>
                            <input type="date" name="tgl_kadaluarsa" value="{{ old('tgl_kadaluarsa') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3 border-t pt-4 dark:border-gray-700">
                <button type="button" @click="openCreate = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Simpan Barang</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit -->
    <x-modal title="Edit Data Barang" x-show="openEdit">
        <form :action="'/barang/' + editItem.id" method="POST" class="space-y-4 p-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" :value="editItem.id">
            @if($errors->any() && request()->has('_method'))
                <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nama Barang</label>
                        <input type="text" name="nama_barang" x-model="editItem.nama_barang" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Kategori</label>
                        <select name="kategori_id" x-model="editItem.kategori_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}" {{ old('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Lokasi Barang</label>
                        <select name="lokasi_barang_id" x-model="editItem.lokasi_barang_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                            <option value="">Pilih Lokasi</option>
                            @foreach(\App\Models\ItemLocation::all() as $loc)
                                <option value="{{ $loc->id }}" {{ old('lokasi_barang_id') == $loc->id ? 'selected' : '' }}>{{ $loc->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Sumber Anggaran</label>
                        <select name="sumber_anggaran_id" x-model="editItem.sumber_anggaran_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                            <option value="">Pilih Sumber</option>
                            @foreach(\App\Models\BudgetSource::all() as $source)
                                <option value="{{ $source->id }}" {{ old('sumber_anggaran_id') == $source->id ? 'selected' : '' }}>{{ $source->nama_sumber }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t pt-4 dark:border-gray-700">
                    <div class="space-y-4">
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Satuan Kecil</p>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-gray-500">Satuan</label>
                            <select name="satuan_kecil_id" x-model="editItem.satuan_kecil_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                                @foreach(\App\Models\Unit::all() as $unit)
                                    <option value="{{ $unit->id }}" {{ old('satuan_kecil_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-gray-500">Harga (Rp)</label>
                            <input type="number" name="harga_satuan_kecil" x-model="editItem.harga_satuan_kecil" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <p class="text-xs font-bold text-purple-600 uppercase tracking-widest">Satuan Besar</p>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-gray-500">Satuan (Opsional)</label>
                            <select name="satuan_besar_id" x-model="editItem.satuan_besar_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                                <option value="">— Tidak Ada —</option>
                                @foreach(\App\Models\Unit::all() as $unit)
                                    <option value="{{ $unit->id }}" {{ old('satuan_besar_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-gray-500">Harga (Rp)</label>
                            <input type="number" name="harga_satuan_besar" x-model="editItem.harga_satuan_besar" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Stok Minimal (Warning)</label>
                        <input type="number" name="stok_minimal" x-model="editItem.stok_minimal" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <input type="text" name="deskripsi" x-model="editItem.deskripsi" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tgl Diterima</label>
                        <input type="date" name="tgl_diterima" x-model="editItem.tgl_diterima" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tgl Kadaluarsa</label>
                        <input type="date" name="tgl_kadaluarsa" x-model="editItem.tgl_kadaluarsa" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3 border-t pt-4 dark:border-gray-700">
                <button type="button" @click="openEdit = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal QR Code -->
    <x-modal title="Label Barang" x-show="openQr">
        <div class="flex flex-col items-center p-6 text-center">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-6 w-full max-w-[240px]">
                <img x-show="editItem.kode_barang" :src="'/barang/qr/' + editItem.kode_barang + '.svg'" class="w-full aspect-square object-contain mx-auto">
                <h4 class="text-sm font-bold text-gray-900 mt-4" x-text="editItem.nama_barang"></h4>
                <p class="text-xs text-primary-600 font-bold" x-text="editItem.kode_barang"></p>
            </div>
            <div class="flex w-full space-x-3">
                <button @click="openQr = false" class="flex-1 py-3 bg-gray-50 text-gray-600 font-bold rounded-xl">Tutup</button>
                <button @click="printLabel(editItem.kode_barang, editItem.nama_barang)" class="flex-1 py-3 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">
                    <i class="fas fa-print mr-2"></i> Cetak
                </button>
            </div>
        </div>
    </x-modal>
</div>
@endsection

@section('scripts')
<script>
function printLabel(kode, nama) {
    const printWindow = window.open('', '_blank', 'width=450,height=550');
    printWindow.document.write(`
        <html>
        <head>
            <title>Cetak Label - ${nama}</title>
            <style>
                body {
                    font-family: 'Courier New', Courier, monospace;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: white;
                }
                .label-card {
                    border: 2px dashed #000;
                    padding: 20px;
                    width: 280px;
                    text-align: center;
                    box-sizing: border-box;
                    background-color: white;
                }
                .header {
                    font-size: 11px;
                    font-weight: bold;
                    margin-bottom: 2px;
                    letter-spacing: 0.5px;
                }
                .subheader {
                    font-size: 10px;
                    margin-bottom: 10px;
                    border-bottom: 1px solid #000;
                    padding-bottom: 8px;
                }
                .qr-image {
                    width: 160px;
                    height: 160px;
                    object-fit: contain;
                    margin: 0 auto;
                }
                .title {
                    font-size: 14px;
                    font-weight: bold;
                    margin-top: 10px;
                    margin-bottom: 4px;
                    word-wrap: break-word;
                }
                .code {
                    font-size: 13px;
                    font-weight: bold;
                    font-family: monospace;
                    background-color: #f1f5f9;
                    padding: 2px 6px;
                    border-radius: 4px;
                    display: inline-block;
                }
                .footer {
                    font-size: 9px;
                    margin-top: 12px;
                    color: #555;
                    border-top: 1px dashed #ccc;
                    padding-top: 6px;
                }
                @media print {
                    body {
                        height: auto;
                        background: none;
                    }
                    .label-card {
                        border: 2px solid #000; /* Solid border for cutting guide */
                        box-shadow: none;
                        margin: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class="label-card">
                <div class="header">BPBD KAB. TASIKMALAYA</div>
                <div class="subheader">LOGISTIK KEBENCANAAN</div>
                <img src="/barang/qr/${kode}.svg" class="qr-image" />
                <div class="title">${nama}</div>
                <div class="code">${kode}</div>
                <div class="footer">SIDARLOG — Sistem Data & Arsip</div>
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(() => window.close(), 250);
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endsection
