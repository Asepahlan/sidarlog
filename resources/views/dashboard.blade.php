@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-8" x-data="dashboardRealtime()" x-init="startPolling()">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Selamat Datang di SIDARLOG</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Status logistik dan operasional inventaris hari ini, {{ now()->translatedFormat('d F Y') }}.</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="flex items-center px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-600 text-[10px] font-bold rounded-lg" x-show="isLive">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span> LIVE
            </span>
            <button class="flex items-center px-4 py-2 bg-white dark:bg-navy-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-semibold text-gray-600 dark:text-gray-300">
                <i class="fas fa-calendar-day mr-2 text-gray-400"></i> {{ now()->format('H:i') }} WIB
            </button>
            @can('laporan.view')
            <a href="{{ route('laporan.index') }}" class="flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all">
                <i class="fas fa-file-pdf mr-2"></i> Cetak Laporan
            </a>
            @endcan
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Barang -->
        <div class="dashboard-card p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center transition-transform hover:scale-105"><i class="fas fa-boxes-stacked text-xl"></i></div>
            </div>
            <p class="text-sm text-slate-500 font-medium">Total Barang</p>
            <h2 class="text-3xl font-bold text-slate-800 mt-1" x-text="stats.total_barang">{{ $stats['total_barang'] }}</h2>
        </div>

        <!-- Total Gudang -->
        <div class="dashboard-card p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center transition-transform hover:scale-105"><i class="fas fa-warehouse text-xl"></i></div>
            </div>
            <p class="text-sm text-slate-500 font-medium">Total Gudang</p>
            <h2 class="text-3xl font-bold text-slate-800 mt-1" x-text="stats.total_gudang">{{ $stats['total_gudang'] }}</h2>
        </div>

        <!-- Barang Kadaluarsa -->
        <div class="dashboard-card p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-yellow-50 text-yellow-500 rounded-xl flex items-center justify-center transition-transform hover:scale-105"><i class="fas fa-hourglass-end text-xl"></i></div>
                <span class="flex items-center text-yellow-600 text-xs font-bold bg-yellow-50 px-2 py-1 rounded-lg animate-pulse" x-show="stats.barang_expired > 0">Kadaluarsa</span>
            </div>
            <p class="text-sm text-slate-500 font-medium">Barang Kadaluarsa</p>
            <h2 class="text-3xl font-bold text-yellow-600 mt-1" x-text="stats.barang_expired">{{ $stats['barang_expired'] }}</h2>
        </div>

        <!-- Stok Minimum -->
        <div class="dashboard-card p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center transition-transform hover:scale-105"><i class="fas fa-triangle-exclamation text-xl"></i></div>
                <span class="flex items-center text-red-600 text-[10px] font-bold bg-red-50 px-2 py-1 rounded-lg animate-pulse" x-show="stats.stok_menipis > 0">Stok Minim</span>
            </div>
            <p class="text-sm text-slate-500 font-medium">Stok Minimum</p>
            <h2 class="text-3xl font-bold text-red-600 mt-1" x-text="stats.stok_menipis">{{ $stats['stok_menipis'] }}</h2>
        </div>
    </div>

    <!-- Alert Tables: Low Stock + Expired -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Stok Rendah -->
        <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-800 bg-orange-50/50 dark:bg-orange-900/10 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-orange-500"></i>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">Barang Stok Rendah</h3>
                    <span class="bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" x-text="lowStockList.length"></span>
                </div>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-navy-800/50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase">Barang</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase">Lokasi</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-center">Stok</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-center">Min</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        <template x-for="item in lowStockList.slice(0, 8)" :key="item.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 cursor-pointer transition-all" @click="window.location.href='/barang/' + item.id">
                                <td class="px-4 py-3">
                                    <p class="text-xs font-bold text-gray-900 dark:text-white" x-text="item.nama_barang"></p>
                                    <p class="text-[10px] text-gray-400 font-mono" x-text="item.kode_barang"></p>
                                </td>
                                <td class="px-4 py-3"><span class="text-[10px] text-gray-500" x-text="item.lokasi"></span></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg" :class="item.stok_saat_ini == 0 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'">
                                        <span x-text="item.stok_saat_ini"></span> <span x-text="item.satuan" class="text-[9px]"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-[10px] text-gray-400" x-text="item.stok_minimal"></td>
                            </tr>
                        </template>
                        <template x-if="lowStockList.length === 0">
                            <tr><td colspan="4" class="px-4 py-8 text-center text-xs text-gray-400"><i class="fas fa-check-circle text-green-400 mr-1"></i> Semua stok aman</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expired / Near Expired -->
        <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-800 bg-red-50/50 dark:bg-red-900/10 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-hourglass-end text-red-500"></i>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">Expired & Mendekati Expired</h3>
                    <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" x-text="expiredList.length + nearExpiryList.length"></span>
                </div>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-navy-800/50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase">Barang</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase">Lokasi</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase">Expired</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        <template x-for="item in expiredList.slice(0, 5)" :key="'exp-'+item.id">
                            <tr class="hover:bg-red-50/50 dark:hover:bg-red-900/10 cursor-pointer transition-all" @click="window.location.href='/barang/' + item.id">
                                <td class="px-4 py-3">
                                    <p class="text-xs font-bold text-gray-900 dark:text-white" x-text="item.nama_barang"></p>
                                    <p class="text-[10px] text-gray-400 font-mono" x-text="item.kode_barang"></p>
                                </td>
                                <td class="px-4 py-3"><span class="text-[10px] text-gray-500" x-text="item.lokasi"></span></td>
                                <td class="px-4 py-3"><span class="text-[10px] text-gray-500" x-text="item.tgl_kadaluarsa"></span></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        <span x-text="item.expired_days"></span> hari lalu
                                    </span>
                                </td>
                            </tr>
                        </template>
                        <template x-for="item in nearExpiryList.slice(0, 5)" :key="'near-'+item.id">
                            <tr class="hover:bg-yellow-50/50 dark:hover:bg-yellow-900/10 cursor-pointer transition-all" @click="window.location.href='/barang/' + item.id">
                                <td class="px-4 py-3">
                                    <p class="text-xs font-bold text-gray-900 dark:text-white" x-text="item.nama_barang"></p>
                                    <p class="text-[10px] text-gray-400 font-mono" x-text="item.kode_barang"></p>
                                </td>
                                <td class="px-4 py-3"><span class="text-[10px] text-gray-500" x-text="item.lokasi"></span></td>
                                <td class="px-4 py-3"><span class="text-[10px] text-gray-500" x-text="item.tgl_kadaluarsa"></span></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        <span x-text="item.days_left"></span> hari lagi
                                    </span>
                                </td>
                            </tr>
                        </template>
                        <template x-if="expiredList.length === 0 && nearExpiryList.length === 0">
                            <tr><td colspan="4" class="px-4 py-8 text-center text-xs text-gray-400"><i class="fas fa-check-circle text-green-400 mr-1"></i> Tidak ada barang expired</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transaction List + Health -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-navy-800/30">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Aktivitas Terakhir</h3>
                    <p class="text-xs text-gray-500 mt-1">Log transaksi logistik terbaru sistem.</p>
                </div>
                <a href="{{ route('laporan.index') }}" class="px-4 py-2 text-xs font-bold text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl transition-all">Lihat Seluruh</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-navy-800/50">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">No. Ref</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Barang</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jenis</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Qty</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Gudang</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($recent_transactions as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                            <td class="px-8 py-5"><span class="text-sm font-bold text-gray-900 dark:text-white">{{ $tx->no_referensi }}</span></td>
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-600 flex items-center justify-center mr-3 text-xs font-bold">{{ substr($tx->barang->nama_barang, 0, 2) }}</div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $tx->barang->nama_barang }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @if($tx->jenis == 'masuk')
                                    <span class="px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 text-[10px] font-bold rounded-full uppercase">Masuk</span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 text-[10px] font-bold rounded-full uppercase">Keluar</span>
                                @endif
                            </td>
                            <td class="px-8 py-5"><span class="text-xs font-bold text-gray-900 dark:text-white">{{ number_format($tx->jumlah_barang_kecil) }} / {{ number_format($tx->jumlah_barang_besar) }}</span></td>
                            <td class="px-8 py-5"><span class="text-xs text-gray-500">{{ $tx->gudang->nama_gudang ?? '-' }}</span></td>
                            <td class="px-8 py-5"><span class="text-xs text-gray-400">{{ $tx->tgl_transaksi->diffForHumans() }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-8 py-12 text-center text-gray-400 italic text-sm">Belum ada transaksi tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Ringkasan Operasional -->
            <div class="bg-white dark:bg-navy-900 p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-6">Aktivitas & Logistik</h3>
                <div class="space-y-5">
                    <!-- Masuk Hari Ini -->
                    <div class="flex items-center justify-between p-3.5 bg-green-50/50 dark:bg-green-950/20 rounded-2xl border border-green-100/30 dark:border-green-900/30">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-905/20 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-arrow-down-long"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase">Masuk Hari Ini</h4>
                                <p class="text-xs text-gray-400 mt-0.5">Barang masuk tercatat</p>
                            </div>
                        </div>
                        <span class="text-lg font-black text-green-600" x-text="operasional.masuk_hari_ini">{{ $operasional['masuk_hari_ini'] }}</span>
                    </div>

                    <!-- Keluar Hari Ini -->
                    <div class="flex items-center justify-between p-3.5 bg-red-50/50 dark:bg-red-950/20 rounded-2xl border border-red-100/30 dark:border-red-900/30">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-905/20 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-arrow-up-long"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase">Keluar Hari Ini</h4>
                                <p class="text-xs text-gray-400 mt-0.5">Barang keluar tercatat</p>
                            </div>
                        </div>
                        <span class="text-lg font-black text-red-600" x-text="operasional.keluar_hari_ini">{{ $operasional['keluar_hari_ini'] }}</span>
                    </div>

                    <!-- Mutasi Hari Ini -->
                    <div class="flex items-center justify-between p-3.5 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-2xl border border-indigo-100/30 dark:border-indigo-900/30">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-905/20 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-shuffle"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase">Mutasi Hari Ini</h4>
                                <p class="text-xs text-gray-400 mt-0.5">Mutasi antar gudang</p>
                            </div>
                        </div>
                        <span class="text-lg font-black text-indigo-600" x-text="operasional.mutasi_hari_ini">{{ $operasional['mutasi_hari_ini'] }}</span>
                    </div>

                    <!-- Opname Bulan Ini -->
                    <div class="flex items-center justify-between p-3.5 bg-amber-50/50 dark:bg-amber-950/20 rounded-2xl border border-amber-100/30 dark:border-amber-900/30">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-905/20 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase">Opname Bulan Ini</h4>
                                <p class="text-xs text-gray-400 mt-0.5">Audit stok dilakukan</p>
                            </div>
                        </div>
                        <span class="text-lg font-black text-amber-600" x-text="operasional.opname_bulan_ini">{{ $operasional['opname_bulan_ini'] }}</span>
                    </div>
                </div>
                @can('system.optimize')
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <form action="{{ route('dashboard.optimize') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-gray-50 dark:bg-navy-800 text-gray-600 dark:text-gray-300 rounded-2xl text-xs font-bold hover:bg-primary-600 hover:text-white transition-all duration-300">Jalankan Maintenance Rutin</button>
                    </form>
                </div>
                @endcan
            </div>

            <!-- Notifikasi Sistem -->
            <div class="bg-navy-900 p-8 rounded-3xl border border-navy-800 shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="font-bold text-white mb-6 flex items-center"><i class="fas fa-bolt-lightning mr-2 text-yellow-400"></i> Notifikasi Sistem</h3>
                    <div class="space-y-4">
                        @forelse($notifications as $notif)
                        @php($targetUrl = isset($notif->data['item_id']) ? route('barang.show', $notif->data['item_id']) : ($notif->data['url'] ?? '/barang'))
                        <a href="{{ $targetUrl }}" class="flex items-start group">
                            <div class="w-2 h-2 rounded-full mt-1.5 mr-3 shrink-0 {{ ($notif->data['type'] ?? '') === 'danger' ? 'bg-red-400' : 'bg-yellow-400' }}"></div>
                            <div>
                                <span class="font-bold text-[10px] uppercase block mb-0.5 {{ ($notif->data['type'] ?? '') === 'danger' ? 'text-red-400' : 'text-yellow-400' }}">{{ $notif->data['title'] ?? 'Notification' }}</span>
                                <p class="text-xs text-gray-300 leading-relaxed group-hover:text-white transition-colors">{{ $notif->data['message'] ?? '' }}</p>
                            </div>
                        </a>
                        @empty
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-green-400 rounded-full mt-1.5 mr-3 shrink-0"></div>
                            <p class="text-xs text-gray-300">Tidak ada notifikasi baru saat ini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-10"><i class="fas fa-shield-halved text-9xl text-white"></i></div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardRealtime() {
    return {
        isLive: true,
        stats: @json($stats),
        operasional: @json($operasional),
        lowStockList: @json($lowStockJson),
        expiredList: @json($expiredJson),
        nearExpiryList: @json($nearExpiryJson),
        startPolling() {
            setInterval(() => this.fetchData(), 30000);
        },
        fetchData() {
            fetch('/dashboard/realtime-data')
                .then(r => r.json())
                .then(d => {
                    this.stats = d.stats;
                    this.operasional = d.operasional;
                    this.lowStockList = d.low_stock;
                    this.expiredList = d.expired;
                    this.nearExpiryList = d.near_expiry;
                    this.isLive = true;
                })
                .catch(() => this.isLive = false);
        }
    }
}
</script>
@endsection
