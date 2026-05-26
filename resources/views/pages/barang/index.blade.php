@extends('layouts.app')

@section('title', 'Master Barang')

@section('content')
<div class="space-y-6" x-data="{ openCreate: {{ $errors->any() && !request()->has('_method') ? 'true' : 'false' }}, openEdit: {{ $errors->any() && request()->has('_method') ? 'true' : 'false' }}, openQr: false, editItem: {} }">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Master Barang</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola daftar inventaris barang, stok, dan klasifikasi sistem.</p>
        </div>
        <div class="flex items-center space-x-3">
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
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-navy-800/50">
                    <tr>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-center">No</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Nama barang</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-right">Stok Kecil</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-right">Harga Kecil</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Sat Kecil</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-right">Stok Besar</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-right">Harga Besar</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Sat Besar</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Sumber Angg.</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Tgl Exp</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Lokasi</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-left">Tgl Terima</th>
                        <th class="px-2 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-2 py-2 text-[11px] text-gray-500 text-center">{{ $index + 1 }}</td>
                        <td class="px-2 py-2 min-w-[120px]">
                            <a href="{{ route('barang.show', $item->id) }}" class="flex flex-col group">
                                <span class="text-[11px] font-bold text-gray-900 dark:text-white leading-tight group-hover:text-primary-600 transition-colors">{{ $item->nama_barang }}</span>
                                <span class="text-[9px] text-gray-500 font-mono mt-0.5">{{ $item->kode_barang }}</span>
                            </a>
                        </td>
                        <td class="px-2 py-2 text-[11px] text-gray-900 dark:text-white text-right">
                            {{ $item->stok_saat_ini_kecil ?? 0 }}
                        </td>
                        <td class="px-2 py-2 text-right">
                            <div class="flex flex-col">
                                <span class="text-[11px] text-gray-900 dark:text-white whitespace-nowrap">Rp {{ number_format($item->harga_satuan_kecil ?? 0, 0, ',', '.') }}</span>
                                <span class="text-[9px] text-gray-500 whitespace-nowrap">Tot: Rp {{ number_format(($item->stok_saat_ini_kecil ?? 0) * ($item->harga_satuan_kecil ?? 0), 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td class="px-2 py-2 text-[11px] text-gray-900 dark:text-white">
                            {{ optional($item->satuanKecil)->nama_satuan ?? '-' }}
                        </td>
                        <td class="px-2 py-2 text-[11px] text-gray-900 dark:text-white text-right">
                            {{ $item->stok_saat_ini_besar ?? 0 }}
                        </td>
                        <td class="px-2 py-2 text-right">
                            <div class="flex flex-col">
                                <span class="text-[11px] text-gray-900 dark:text-white whitespace-nowrap">Rp {{ number_format($item->harga_satuan_besar ?? 0, 0, ',', '.') }}</span>
                                <span class="text-[9px] text-gray-500 whitespace-nowrap">Tot: Rp {{ number_format(($item->stok_saat_ini_besar ?? 0) * ($item->harga_satuan_besar ?? 0), 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td class="px-2 py-2 text-[11px] text-gray-900 dark:text-white">
                            {{ optional($item->satuanBesar)->nama_satuan ?? '-' }}
                        </td>
                        <td class="px-2 py-2 text-[11px] text-gray-900 dark:text-white leading-tight min-w-[80px]">
                            {{ optional($item->sumberAnggaran)->nama_sumber ?? '-' }}
                        </td>
                        <td class="px-2 py-2 text-[11px] text-gray-900 dark:text-white whitespace-nowrap">
                            @if($item->tgl_kadaluarsa)
                                <span class="{{ $item->tgl_kadaluarsa < \Carbon\Carbon::today() ? 'text-red-600 font-bold' : ($item->tgl_kadaluarsa <= \Carbon\Carbon::today()->addDays(30) ? 'text-yellow-600 font-bold' : '') }}">
                                    {{ $item->tgl_kadaluarsa->format('d/m/y') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-2 py-2 text-[11px] text-gray-900 dark:text-white leading-tight min-w-[80px]">
                            {{ optional($item->lokasiBarang)->nama_lokasi ?? '-' }}
                        </td>
                        <td class="px-2 py-2 text-[11px] text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $item->tgl_diterima ? $item->tgl_diterima->format('d/m/y') : '-' }}
                        </td>
                        <td class="px-2 py-2 text-center space-x-2 whitespace-nowrap">
                            <a href="{{ route('barang.show', $item->id) }}" class="text-gray-400 hover:text-primary-600 transition-colors" title="Detail Barang">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button @click="editItem = JSON.parse($el.dataset.item); openQr = true" data-item="{{ json_encode(['id'=>$item->id,'kode_barang'=>$item->kode_barang,'nama_barang'=>$item->nama_barang]) }}" class="text-gray-400 hover:text-primary-600 transition-colors" title="QR Label">
                                <i class="fas fa-qrcode"></i>
                            </button>
                            @can('barang.edit')
                            <button @click="editItem = JSON.parse($el.dataset.item); openEdit = true" data-item="{{ json_encode(['id'=>$item->id,'nama_barang'=>$item->nama_barang,'kategori_id'=>$item->kategori_id,'lokasi_barang_id'=>$item->lokasi_barang_id,'sumber_anggaran_id'=>$item->sumber_anggaran_id,'satuan_kecil_id'=>$item->satuan_kecil_id,'satuan_besar_id'=>$item->satuan_besar_id,'harga_satuan_kecil'=>$item->harga_satuan_kecil,'harga_satuan_besar'=>$item->harga_satuan_besar,'stok_minimal'=>$item->stok_minimal,'deskripsi'=>$item->deskripsi,'tgl_diterima'=>$item->tgl_diterima?$item->tgl_diterima->format('Y-m-d'):null,'tgl_kadaluarsa'=>$item->tgl_kadaluarsa?$item->tgl_kadaluarsa->format('Y-m-d'):null]) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endcan
                            @can('barang.delete')
                            <form id="delete-form-{{ $item->id }}" action="{{ route('barang.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('delete-form-{{ $item->id }}', 'Hapus Barang?', 'Apakah Anda yakin ingin menghapus barang ' + '{{ $item->nama_barang }}' + '? Seluruh riwayat stok barang ini juga akan ikut terhapus.')" class="text-red-600 hover:text-red-800 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="px-6 py-12 text-center">
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
        <form action="{{ route('barang.store') }}" method="POST" class="space-y-4 p-4">
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
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                            Kode Barang
                            <span class="text-xs font-normal text-gray-400 ml-1">(opsional, otomatis jika kosong)</span>
                        </label>
                        <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none" placeholder="Contoh: BRG-001 (biarkan kosong untuk auto)">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nama Barang</label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Kategori</label>
                        <select name="kategori_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}" {{ old('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
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

                <div class="grid grid-cols-2 gap-4 border-t pt-4 dark:border-gray-700">
                    <div class="space-y-4">
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Konfigurasi Satuan Kecil</p>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-gray-500">Satuan <span class="text-red-500">*</span></label>
                            <select name="satuan_kecil_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                                <option value="">Pilih Satuan</option>
                                @foreach(\App\Models\Unit::all() as $unit)
                                    <option value="{{ $unit->id }}" {{ old('satuan_kecil_id') == $unit->id || old('satuan_besar_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-gray-500">Harga (Rp)</label>
                            <input type="number" name="harga_satuan_kecil" value="{{ old('harga_satuan_kecil') }}" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <p class="text-xs font-bold text-purple-600 uppercase tracking-widest">Konfigurasi Satuan Besar</p>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-medium text-gray-500">Satuan (Opsional)</label>
                            <select name="satuan_besar_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                                <option value="">— Tidak Ada —</option>
                                @foreach(\App\Models\Unit::all() as $unit)
                                    <option value="{{ $unit->id }}" {{ old('satuan_kecil_id') == $unit->id || old('satuan_besar_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
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
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Stok Minimal (Warning)</label>
                        <input type="number" name="stok_minimal" value="{{ old('stok_minimal', 0) }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
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

                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">{{ old('deskripsi') }}</textarea>
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
                                    <option value="{{ $unit->id }}" {{ old('satuan_kecil_id') == $unit->id || old('satuan_besar_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
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
                                    <option value="{{ $unit->id }}" {{ old('satuan_kecil_id') == $unit->id || old('satuan_besar_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_satuan }}</option>
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
                <button class="flex-1 py-3 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">
                    <i class="fas fa-print mr-2"></i> Cetak
                </button>
            </div>
        </div>
    </x-modal>
</div>
@endsection
