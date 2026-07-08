@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
<div class="space-y-6" x-data="{ activeTab: '{{ $isFiltered ? 'input' : 'daftar' }}' }">
    
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Stock Opname</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Audit kecocokan stok sistem dengan kondisi fisik logistik di gudang.</p>
        </div>
        
        <!-- Tab Navigation Switcher -->
        <div class="flex bg-gray-100 dark:bg-navy-950 p-1 rounded-2xl border border-gray-200 dark:border-gray-800">
            <button @click="activeTab = 'daftar'" 
                    :class="activeTab === 'daftar' ? 'bg-white dark:bg-navy-900 text-primary-600 dark:text-white font-bold shadow-md' : 'text-gray-500 dark:text-gray-400'"
                    class="px-4 py-2 text-xs rounded-xl transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-list-check"></i> Daftar Laporan
            </button>
            <button @click="activeTab = 'input'" 
                    :class="activeTab === 'input' ? 'bg-white dark:bg-navy-900 text-primary-600 dark:text-white font-bold shadow-md' : 'text-gray-500 dark:text-gray-400'"
                    class="px-4 py-2 text-xs rounded-xl transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-pen-to-square"></i> Input & Filter Opname
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-2xl flex items-center gap-3 animate-fade-in" role="alert">
        <i class="fas fa-check-circle text-lg text-green-500"></i>
        <span class="block sm:inline font-medium text-xs">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-2xl flex items-center gap-3 animate-fade-in" role="alert">
        <i class="fas fa-exclamation-circle text-lg text-red-500"></i>
        <span class="block sm:inline font-medium text-xs">{{ session('error') }}</span>
    </div>
    @endif

    {{-- ==================== TAB 1: DAFTAR LAPORAN (HISTORY) ==================== --}}
    <div x-show="activeTab === 'daftar'" class="space-y-6">
        <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 dark:bg-navy-800/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center" style="width: 5%">No</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor Dokumen SO</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gudang</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Periode</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Tanggal Opname</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Petugas</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($historyOpnames as $index => $history)
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500">{{ $historyOpnames->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $history->nomor_stock_opname }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300 font-medium">
                                {{ $history->gudang->nama_gudang ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-600 dark:text-gray-400 font-mono">
                                {{ $history->periode }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-600 dark:text-gray-400">
                                {{ $history->tgl_opname ? $history->tgl_opname->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                <div>{{ $history->petugas_nama }}</div>
                                <div class="text-[10px] text-gray-400">Kepala Gudang: {{ $history->kepala_gudang_nama }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center space-x-1">
                                <!-- Print Icon triggers setup view directly back to filters -->
                                <a href="{{ route('stock-opname.index', ['gudang_id' => $history->gudang_id, 'periode' => $history->periode, 'tgl_opname' => $history->tgl_opname ? $history->tgl_opname->format('Y-m-d') : '']) }}"
                                   class="inline-flex items-center px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded font-bold text-[10px] transition-all">
                                    <i class="fas fa-edit mr-1"></i> Edit/Buka
                                </a>
                                <a href="{{ route('stock-opname.print', ['gudang_id' => $history->gudang_id, 'periode' => $history->periode, 'tgl_opname' => $history->tgl_opname ? $history->tgl_opname->format('Y-m-d') : '', 'preview' => 1]) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold text-[10px] transition-all">
                                    <i class="fas fa-eye mr-1"></i> Preview
                                </a>
                                <a href="{{ route('stock-opname.print', ['gudang_id' => $history->gudang_id, 'periode' => $history->periode, 'tgl_opname' => $history->tgl_opname ? $history->tgl_opname->format('Y-m-d') : '']) }}"
                                   class="inline-flex items-center px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded font-bold text-[10px] transition-all">
                                    <i class="fas fa-file-pdf mr-1"></i> PDF
                                </a>
                                <a href="{{ route('stock-opname.excel', ['gudang_id' => $history->gudang_id, 'periode' => $history->periode, 'tgl_opname' => $history->tgl_opname ? $history->tgl_opname->format('Y-m-d') : '']) }}"
                                   class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded font-bold text-[10px] transition-all">
                                    <i class="fas fa-file-excel mr-1"></i> Excel
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <i class="fas fa-clipboard-check text-4xl text-gray-350 dark:text-gray-700"></i>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada dokumen stock opname yang tersimpan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($historyOpnames->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $historyOpnames->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ==================== TAB 2: BUAT & FILTER LAPORAN ==================== --}}
    <div x-show="activeTab === 'input'" class="space-y-6">
        
        <!-- Filter Card -->
        <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-150 dark:border-gray-800 shadow-sm p-6">
            <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-850 pb-3 mb-5">
                <i class="fas fa-filter text-primary-500 text-sm"></i>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Filter Target Stock Opname</h2>
            </div>
            <form action="{{ route('stock-opname.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end text-xs">
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Gudang <span class="text-red-500">*</span></label>
                    <select name="gudang_id" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all font-medium">
                        <option value="">-- Pilih Gudang --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ request('gudang_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->nama_gudang }}
                                @if($wh->stok_total > 0)
                                    (Stok: {{ number_format($wh->stok_total) }} unit)
                                @elseif($wh->stok_total == 0)
                                    (Kosong)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[9px] text-amber-600 dark:text-amber-400">
                        <i class="fas fa-circle-info"></i> Pilih gudang yang memiliki stok aktif untuk hasil yang akurat.
                    </p>
                </div>

                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Periode <span class="text-red-500">*</span></label>
                    <input type="text" name="periode" value="{{ request('periode', $metadata['periode'] ?: 'Juni 2026') }}" required placeholder="Contoh: Juni 2026"
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all font-bold">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Tanggal Opname <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_opname" value="{{ request('tgl_opname', $metadata['tgl_opname'] ?: date('Y-m-d')) }}" required
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all font-bold">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Kategori (Opsional)</label>
                    <select name="kategori_id" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-md shrink-0">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        @if($isFiltered)
        
        {{-- ⚠️ Peringatan stok 0 jika semua item kosong --}}
        @php $totalStokSistem = $items->sum('stok_sistem_gudang'); @endphp
        @if($totalStokSistem == 0 && $items->count() > 0)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700 rounded-2xl px-5 py-4 flex items-start gap-3">
            <i class="fas fa-triangle-exclamation text-amber-500 text-lg mt-0.5 shrink-0"></i>
            <div class="text-xs text-amber-800 dark:text-amber-300">
                <p class="font-bold text-sm mb-1">Stok Sistem = 0 untuk semua barang di gudang ini</p>
                <p>Gudang yang Anda pilih (<strong>{{ $warehouses->firstWhere('id', request('gudang_id'))->nama_gudang ?? '-' }}</strong>) belum memiliki transaksi masuk yang tercatat. Kemungkinan transaksi stok berada di gudang lain.</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($warehouses->where('stok_total', '>', 0) as $wh)
                    <a href="{{ route('stock-opname.index', array_merge(request()->all(), ['gudang_id' => $wh->id])) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 dark:bg-amber-800 hover:bg-amber-200 dark:hover:bg-amber-700 text-amber-800 dark:text-amber-200 rounded-lg font-bold text-[10px] transition-all">
                        <i class="fas fa-box-open"></i> 
                        Ganti ke: {{ $wh->nama_gudang }} ({{ number_format($wh->stok_total) }} unit)
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ==================== TABEL INPUT BATCH & METADATA DOKUMEN ==================== --}}

        <form action="{{ route('stock-opname.save-batch') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6"
              x-data="{
                items: [
                    @foreach($items as $index => $item)
                    {
                        barang_id: {{ $item->id }},
                        kode_barang: '{{ $item->kode_barang }}',
                        nama_barang: '{{ $item->nama_barang }}',
                        satuan: '{{ $item->satuanKecil->nama_satuan ?? 'Pcs' }}',
                        stok_sistem: {{ $item->stok_sistem_gudang }},
                        stok_fisik: {{ $existingOpnames->has($item->id) ? $existingOpnames->get($item->id)->stok_fisik : $item->stok_sistem_gudang }},
                        kondisi_barang: '{{ $existingOpnames->has($item->id) ? $existingOpnames->get($item->id)->kondisi_barang : 'Baik' }}',
                        keterangan: '{{ $existingOpnames->has($item->id) ? $existingOpnames->get($item->id)->keterangan : '' }}'
                    },
                    @endforeach
                ],
                petugasNama: '{{ $metadata['petugas_nama'] }}',
                petugasNip: '{{ $metadata['petugas_nip'] }}',
                kepalaGudangNama: '{{ $metadata['kepala_gudang_nama'] }}',
                kepalaGudangNip: '{{ $metadata['kepala_gudang_nip'] }}',
                mengetahuiNama: '{{ $metadata['mengetahui_nama'] }}',
                mengetahuiNip: '{{ $metadata['mengetahui_nip'] }}',

                // Sync signature values from selectors
                syncSignatures() {
                    const pet = document.getElementById('petugas_select');
                    if (pet && pet.value) {
                        const opt = pet.options[pet.selectedIndex];
                        this.petugasNama = opt.getAttribute('data-nama');
                        this.petugasNip = opt.getAttribute('data-nip') || '-';
                    }
                    const kg = document.getElementById('kepala_gudang_select');
                    if (kg && kg.value) {
                        const opt = kg.options[kg.selectedIndex];
                        this.kepalaGudangNama = opt.getAttribute('data-nama');
                        this.kepalaGudangNip = opt.getAttribute('data-nip') || '-';
                    }
                    const meng = document.getElementById('mengetahui_select');
                    if (meng && meng.value) {
                        const opt = meng.options[meng.selectedIndex];
                        this.mengetahuiNama = opt.getAttribute('data-nama');
                        this.mengetahuiNip = opt.getAttribute('data-nip') || '-';
                    }
                }
              }" x-init="syncSignatures()">
            
            @csrf
            
            <!-- Hidden Filter Fields -->
            <input type="hidden" name="gudang_id" value="{{ request('gudang_id') }}">
            <input type="hidden" name="periode" value="{{ request('periode') }}">
            <input type="hidden" name="tgl_opname" value="{{ request('tgl_opname') }}">
            <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}">

            <!-- Left Panel: Metadata Dokumen & Tanda Tangan (Col 4) -->
            <div class="lg:col-span-4 space-y-6 text-xs">
                
                <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-150 dark:border-gray-800 shadow-sm p-6 space-y-5">
                    <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                        <i class="fas fa-folder-open text-primary-500 text-sm"></i>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Detail Dokumen</h2>
                    </div>

                    <!-- Nomor Dokumen Manual -->
                    <div class="flex flex-col gap-1.5">
                        <label class="block font-bold text-gray-700 dark:text-gray-300">Nomor Dokumen Laporan <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_stock_opname" value="{{ old('nomor_stock_opname', $metadata['nomor_stock_opname']) }}" required
                               class="w-full px-3 py-2 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl font-bold text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                               placeholder="Contoh: 005/SO/BPBD/2026">
                        <p class="text-[10px] text-gray-400 leading-normal">Silakan isi nomor dokumen laporan hasil stock opname secara manual.</p>
                    </div>

                    <!-- Detail Filter Aktif -->
                    <div class="p-3 bg-gray-50 dark:bg-navy-950 rounded-xl space-y-2 border border-gray-100 dark:border-gray-800">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase block">Gudang Audit</span>
                            <span class="font-bold text-gray-800 dark:text-white">
                                {{ $warehouses->firstWhere('id', request('gudang_id'))->nama_gudang }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Periode</span>
                                <span class="font-bold text-gray-800 dark:text-white">{{ request('periode') }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Tanggal</span>
                                <span class="font-bold text-gray-800 dark:text-white">
                                    {{ \Carbon\Carbon::parse(request('tgl_opname'))->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tanda Tangan Card -->
                <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-150 dark:border-gray-800 shadow-sm p-6 space-y-4">
                    <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                        <i class="fas fa-signature text-primary-500 text-sm"></i>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Pejabat Penandatangan</h2>
                    </div>

                    <!-- 1. Petugas Opname -->
                    <div class="space-y-1.5">
                        <label class="block font-bold text-gray-700 dark:text-gray-300">Petugas Stock Opname</label>
                        <select id="petugas_select" @change="syncSignatures()" 
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl font-medium outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                            <option value="">-- Pilih Petugas --</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->id }}" data-nama="{{ $usr->nama_lengkap ?? $usr->name }}" data-nip="{{ $usr->nip ?? '-' }}"
                                    {{ ($metadata['petugas_nama'] == ($usr->nama_lengkap ?? $usr->name)) ? 'selected' : '' }}>
                                    {{ $usr->nama_lengkap ?? $usr->name }} (NIP. {{ $usr->nip ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="petugas_nama" x-model="petugasNama">
                        <input type="hidden" name="petugas_nip" x-model="petugasNip">
                    </div>

                    <!-- 2. Kepala Gudang -->
                    <div class="space-y-1.5">
                        <label class="block font-bold text-gray-700 dark:text-gray-300">Kepala Gudang</label>
                        <select id="kepala_gudang_select" @change="syncSignatures()" 
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl font-medium outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                            <option value="">-- Pilih Kepala Gudang --</option>
                            @foreach($firstParties as $party)
                                <option value="{{ $party->id }}" data-nama="{{ $party->nama_pihak }}" data-nip="{{ $party->nip }}"
                                    {{ ($metadata['kepala_gudang_nama'] == $party->nama_pihak) ? 'selected' : '' }}>
                                    {{ $party->nama_pihak }} (NIP. {{ $party->nip }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="kepala_gudang_nama" x-model="kepalaGudangNama">
                        <input type="hidden" name="kepala_gudang_nip" x-model="kepalaGudangNip">
                    </div>

                    <!-- 3. Mengetahui -->
                    <div class="space-y-1.5">
                        <label class="block font-bold text-gray-700 dark:text-gray-300">Mengetahui (Pimpinan/Kepala Pelaksana)</label>
                        <select id="mengetahui_select" @change="syncSignatures()" 
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl font-medium outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                            @foreach($firstParties as $party)
                                <option value="{{ $party->id }}" data-nama="{{ $party->nama_pihak }}" data-nip="{{ $party->nip }}"
                                    {{ ($metadata['mengetahui_nama'] == $party->nama_pihak) ? 'selected' : '' }}>
                                    {{ $party->nama_pihak }} (NIP. {{ $party->nip }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="mengetahui_nama" x-model="mengetahuiNama">
                        <input type="hidden" name="mengetahui_nip" x-model="mengetahuiNip">
                    </div>
                </div>
            </div>

            <!-- Right Panel: Tabel Batch Input & Aksi (Col 8) -->
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-150 dark:border-gray-800 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-800 pb-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-boxes-stacked text-primary-500 text-sm"></i>
                            <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Item Barang Audit</h2>
                        </div>
                        <span class="bg-primary-50 text-primary-700 dark:bg-primary-950/50 dark:text-primary-400 font-bold px-2 py-0.5 rounded text-[10px]" x-text="items.length + ' Barang'"></span>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto border border-gray-100 dark:border-gray-800 rounded-2xl">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 dark:bg-navy-800">
                                <tr>
                                    <th class="px-3 py-3 text-center" style="width: 5%">No</th>
                                    <th class="px-3 py-3" style="width: 35%">Barang</th>
                                    <th class="px-3 py-3 text-center" style="width: 10%">Satuan</th>
                                    <th class="px-3 py-3 text-right" style="width: 10%">Sistem</th>
                                    <th class="px-3 py-3 text-center" style="width: 12%">Stok Fisik</th>
                                    <th class="px-3 py-3 text-right" style="width: 10%">Selisih</th>
                                    <th class="px-3 py-3" style="width: 18%">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium">
                                <template x-for="(item, index) in items" :key="item.barang_id">
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-navy-950/20">
                                        <!-- No -->
                                        <td class="px-3 py-2 text-center text-gray-500" x-text="index + 1"></td>
                                        
                                        <!-- Barang -->
                                        <td class="px-3 py-2">
                                            <input type="hidden" :name="'items['+index+'][barang_id]'" :value="item.barang_id">
                                            <div class="font-bold text-gray-900 dark:text-white" x-text="item.nama_barang"></div>
                                            <div class="text-[9px] text-gray-400 font-mono" x-text="item.kode_barang"></div>
                                        </td>
                                        
                                        <!-- Satuan -->
                                        <td class="px-3 py-2 text-center text-gray-500" x-text="item.satuan"></td>
                                        
                                        <!-- Stok Sistem -->
                                        <td class="px-3 py-2 text-right font-bold text-gray-650 dark:text-gray-400">
                                            <input type="hidden" :name="'items['+index+'][stok_sistem]'" :value="item.stok_sistem">
                                            <span x-text="item.stok_sistem"></span>
                                        </td>
                                        
                                        <!-- Stok Fisik (Input) -->
                                        <td class="px-3 py-2 text-center">
                                            <input type="number" :name="'items['+index+'][stok_fisik]'" x-model.number="item.stok_fisik" min="0" required
                                                   class="w-20 px-2 py-1 text-center bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                                        </td>
                                        
                                        <!-- Selisih -->
                                        <td class="px-3 py-2 text-right font-bold"
                                            :class="(item.stok_fisik - item.stok_sistem) == 0 ? 'text-gray-800 dark:text-gray-300' : ((item.stok_fisik - item.stok_sistem) > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400')">
                                            <span x-text="((item.stok_fisik - item.stok_sistem) > 0 ? '+' : '') + (item.stok_fisik - item.stok_sistem)"></span>
                                        </td>
                                        
                                        <!-- Kondisi & Keterangan -->
                                        <td class="px-3 py-2 space-y-1">
                                            <select :name="'items['+index+'][kondisi_barang]'" x-model="item.kondisi_barang"
                                                    class="w-full px-2 py-1 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-lg text-[10px] outline-none">
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak">Rusak</option>
                                                <option value="Kadaluarsa">Kadaluarsa</option>
                                                <option value="Hilang">Hilang</option>
                                            </select>
                                            <input type="text" :name="'items['+index+'][keterangan]'" x-model="item.keterangan" placeholder="Catatan selisih..."
                                                   class="w-full px-2 py-1 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-lg text-[10px] outline-none placeholder-gray-400">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-gray-50 dark:border-gray-850">
                        <div class="text-[10px] text-gray-400">
                            💡 <em>Perubahan stok fisik yang tidak sesuai sistem akan memicu pencatatan jurnal penyesuaian otomatis di database.</em>
                        </div>
                        <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5 text-xs">
                                <i class="fas fa-save"></i> Simpan Hasil
                            </button>
                            <a href="{{ route('stock-opname.print', [
                                'gudang_id'          => request('gudang_id'),
                                'periode'            => request('periode'),
                                'tgl_opname'         => request('tgl_opname'),
                                'kategori_id'        => request('kategori_id'),
                                'preview'            => 1
                            ]) }}" target="_blank"
                               class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5 text-xs">
                                <i class="fas fa-eye"></i> Preview
                            </a>
                            <a href="{{ route('stock-opname.print', [
                                'gudang_id'          => request('gudang_id'),
                                'periode'            => request('periode'),
                                'tgl_opname'         => request('tgl_opname'),
                                'kategori_id'        => request('kategori_id'),
                            ]) }}"
                               class="w-full sm:w-auto px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5 text-xs">
                                <i class="fas fa-file-pdf"></i> Cetak PDF
                            </a>
                            <a href="{{ route('stock-opname.excel', [
                                'gudang_id'          => request('gudang_id'),
                                'periode'            => request('periode'),
                                'tgl_opname'         => request('tgl_opname'),
                                'kategori_id'        => request('kategori_id'),
                            ]) }}"
                               class="w-full sm:w-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5 text-xs">
                                <i class="fas fa-file-excel"></i> Cetak Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @else
        <!-- Box Info: Silakan Filter Dahulu -->
        <div class="bg-amber-50/50 dark:bg-amber-900/10 border border-amber-250 dark:border-amber-900/35 rounded-3xl p-8 text-center text-xs text-amber-700 dark:text-amber-400">
            <div class="max-w-md mx-auto space-y-2">
                <i class="fas fa-circle-info text-3xl text-amber-500 mb-2"></i>
                <p class="font-bold text-sm">Form Input / Cetak Stock Opname Belum Siap</p>
                <p class="text-gray-500 leading-relaxed">Silakan tentukan <strong>Gudang, Periode, dan Tanggal Stock Opname</strong> di atas terlebih dahulu, kemudian klik <strong>Tampilkan</strong> untuk melakukan pencatatan atau mencetak laporan.</p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
