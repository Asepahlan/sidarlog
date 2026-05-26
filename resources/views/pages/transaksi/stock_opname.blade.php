@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
<div class="space-y-6" x-data="{ openCreate: false }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Stock Opname</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Audit kecocokan stok sistem dengan stok fisik di gudang.</p>
        </div>
        <button @click="openCreate = true" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/30">
            <i class="fas fa-clipboard-check mr-2"></i> Mulai Opname
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3" role="alert">
        <i class="fas fa-check-circle text-lg"></i>
        <span class="block sm:inline font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-navy-800/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Barang</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Sistem</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Fisik</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Selisih</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Auditor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($opnames as $op)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $op->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $op->barang->nama_barang }}</p>
                            <p class="text-[10px] text-gray-400 uppercase">{{ $op->gudang->nama_gudang }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">{{ $op->stok_sistem }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ $op->stok_fisik }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($op->selisih == 0)
                                <span class="text-green-500 font-bold text-xs">Match</span>
                            @else
                                <span class="px-2 py-1 {{ $op->selisih > 0 ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }} text-xs font-bold rounded">
                                    {{ $op->selisih > 0 ? '+' : '' }}{{ $op->selisih }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ $op->pengguna->nama_lengkap }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i class="fas fa-clipboard-check text-4xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada riwayat stock opname.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Opname -->
    <x-modal title="Input Hasil Stock Opname" x-show="openCreate">
        <form action="{{ route('stock-opname.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-5">
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Barang</label>
                    <select name="barang_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        <option value="">Pilih Barang yang di-Audit</option>
                        @foreach(\App\Models\Item::all() as $item)
                            <option value="{{ $item->id }}">{{ $item->kode_barang }} - {{ $item->nama_barang }} (Sistem: {{ $item->current_stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Lokasi Gudang</label>
                    <select name="gudang_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        @foreach(\App\Models\Warehouse::all() as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Jumlah Fisik yang Ditemukan</label>
                    <input type="number" name="stok_fisik" required min="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none" placeholder="Masukkan jumlah aktual di gudang">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Keterangan / Alasan Selisih</label>
                    <textarea name="keterangan" rows="3" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none" placeholder="Contoh: Barang rusak, salah hitung sebelumnya, dsb."></textarea>
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-xl border border-yellow-100 dark:border-yellow-900/30">
                <p class="text-[10px] text-yellow-700 dark:text-yellow-400 font-bold uppercase mb-1">💡 INFO SISTEM</p>
                <p class="text-xs text-yellow-600 dark:text-yellow-500 leading-relaxed">Sistem akan secara otomatis membuat transaksi penyesuaian (*adjustment*) jika terdapat selisih antara stok sistem dan stok fisik yang Anda masukkan.</p>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openCreate = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Proses & Sesuaikan Stok</button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
