@extends('layouts.app')

@section('title', 'Mutasi Antar Gudang')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Mutasi Antar Gudang</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Riwayat perpindahan stok barang antar lokasi gudang.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('laporan.mutasi.excel', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-500/20">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </a>
            <a href="{{ route('laporan.mutasi.pdf', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-rose-500/20">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>
            <a href="{{ route('mutasi-gudang.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/30">
                <i class="fas fa-shuffle mr-2"></i> Buat Mutasi Baru
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <!-- Search & Filter Bar -->
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-navy-800/20">
            <form action="{{ route('mutasi-gudang.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Mutasi atau barang..." class="w-full pl-9 pr-4 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                    </div>
                    <div>
                        <select name="gudang_asal_id" class="w-full px-3 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                            <option value="">Gudang Asal</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('gudang_asal_id') == $wh->id ? 'selected' : '' }}>{{ $wh->nama_gudang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="gudang_tujuan_id" class="w-full px-3 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                            <option value="">Gudang Tujuan</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('gudang_tujuan_id') == $wh->id ? 'selected' : '' }}>{{ $wh->nama_gudang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" title="Tanggal Mulai">
                    </div>
                    <div>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" title="Tanggal Akhir">
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-5 md:pt-0">
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl text-xs shadow-md transition-all">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'gudang_asal_id', 'gudang_tujuan_id', 'start_date', 'end_date']))
                        <a href="{{ route('mutasi-gudang.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-all">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-navy-800/50">
                    <tr>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">No. Mutasi</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Barang</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Asal → Tujuan</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jumlah</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($mutations as $mut)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-4 py-5 text-sm font-bold text-primary-600">{{ $mut->no_mutasi }}</td>
                        <td class="px-4 py-5">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $mut->barang->nama_barang }}</p>
                            <p class="text-[10px] text-gray-400">{{ $mut->barang->kode_barang }}</p>
                        </td>
                        <td class="px-4 py-5">
                            <div class="flex items-center space-x-2 text-sm">
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $mut->gudangAsal->nama_gudang }}</span>
                                <i class="fas fa-arrow-right text-primary-500 text-xs"></i>
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $mut->gudangTujuan->nama_gudang }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-5 text-sm font-bold text-gray-900 dark:text-white">{{ number_format($mut->jumlah_barang_kecil) }} unit</td>
                        <td class="px-4 py-5">
                            @php
                                $statusColor = match($mut->status) {
                                    'approved' => 'bg-green-50 text-green-600',
                                    'pending'  => 'bg-yellow-50 text-yellow-600',
                                    'rejected' => 'bg-red-50 text-red-600',
                                    default    => 'bg-gray-50 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 {{ $statusColor }} text-[10px] font-bold rounded uppercase">{{ $mut->status }}</span>
                        </td>
                        <td class="px-4 py-5 text-sm text-gray-500">{{ $mut->tgl_mutasi->format('d M Y') }}</td>
                        <td class="px-4 py-5 text-right">
                            <form id="delete-form-{{ $mut->id }}" action="{{ route('mutasi-gudang.destroy', $mut->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('delete-form-{{ $mut->id }}', 'Batalkan Mutasi?', 'Apakah Anda yakin ingin membatalkan mutasi {{ $mut->no_mutasi }}? Stok barang di gudang asal dan tujuan akan disesuaikan kembali secara otomatis.')" class="text-red-600 hover:text-red-800 transition-colors text-sm" title="Batalkan Mutasi">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-navy-800 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-shuffle text-2xl text-gray-300 dark:text-gray-600"></i>
                                </div>
                                <p class="text-gray-400 italic text-sm">Belum ada data mutasi gudang.</p>
                                <a href="{{ route('mutasi-gudang.create') }}" class="text-primary-600 text-sm font-bold hover:underline">Buat mutasi pertama →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mutations->hasPages())
        <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $mutations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
