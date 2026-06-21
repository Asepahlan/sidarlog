@extends('layouts.app')

@section('title', 'Detail Barang - ' . $item->nama_barang)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('barang.index') }}" class="inline-flex items-center justify-center w-10 h-10 bg-white dark:bg-navy-900 border border-gray-200 dark:border-gray-800 rounded-xl text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Detail Inventaris</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Informasi lengkap dan riwayat mutasi barang.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            @can('barang.edit')
            <a href="{{ route('barang.edit', $item->id) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/20 text-sm">
                <i class="fas fa-edit mr-2"></i> Edit Barang
            </a>
            @endcan
            
            @can('barang.delete')
            <form id="delete-form-{{ $item->id }}" action="{{ route('barang.destroy', $item->id) }}" method="POST" class="inline flex-1 sm:flex-none">
                @csrf
                @method('DELETE')
                <button type="button" @click="triggerDelete('delete-form-{{ $item->id }}', 'Hapus Barang?', 'Apakah Anda yakin ingin menghapus barang {{ $item->nama_barang }}? Seluruh riwayat stok barang ini juga akan ikut terhapus.')" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-red-500/20 text-sm">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Detail Card & QR Code -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- General Info Card -->
            <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-2">
                            {{ optional($item->kategori)->nama_kategori ?? 'Kategori Lain' }}
                        </span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">{{ $item->nama_barang }}</h2>
                        <p class="text-sm font-mono text-gray-500 dark:text-gray-400 mt-1">{{ $item->kode_barang }}</p>
                    </div>
                    <div class="flex flex-col items-center gap-2">
                        <div class="bg-gray-50 dark:bg-navy-800 p-2.5 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <img src="{{ route('barang.qr', $item->kode_barang) }}" class="w-20 h-20 object-contain" alt="QR Code">
                        </div>
                        <button onclick="printLabel('{{ $item->kode_barang }}', '{{ $item->nama_barang }}')" class="inline-flex items-center text-xs font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                            <i class="fas fa-print mr-1"></i> Cetak Label
                        </button>
                    </div>
                </div>

                <hr class="border-gray-100 dark:border-gray-800">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <!-- Stok Kecil -->
                    <div class="bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl p-5 border border-blue-100/50 dark:border-blue-900/20 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-blue-600/80 dark:text-blue-400/80 uppercase tracking-wider mb-1">Stok Satuan Kecil</p>
                            <h3 class="text-3xl font-extrabold text-blue-900 dark:text-blue-300">{{ $item->stok_saat_ini_kecil ?? 0 }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Satuan: <span class="font-bold text-gray-700 dark:text-gray-300">{{ optional($item->satuanKecil)->nama_satuan ?? '-' }}</span></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="fas fa-boxes-stacked text-xl"></i>
                        </div>
                    </div>

                    <!-- Stok Besar -->
                    <div class="bg-purple-50/50 dark:bg-purple-900/10 rounded-2xl p-5 border border-purple-100/50 dark:border-purple-900/20 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-purple-600/80 dark:text-purple-400/80 uppercase tracking-wider mb-1">Stok Satuan Besar</p>
                            <h3 class="text-3xl font-extrabold text-purple-900 dark:text-purple-300">{{ $item->stok_saat_ini_besar ?? 0 }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Satuan: <span class="font-bold text-gray-700 dark:text-gray-300">{{ optional($item->satuanBesar)->nama_satuan ?? '-' }}</span></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <i class="fas fa-box text-xl"></i>
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                    
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Harga & Nilai Aset</h4>
                        <div class="space-y-3 bg-gray-50 dark:bg-navy-800/40 p-5 rounded-2xl border border-gray-100/50 dark:border-gray-800">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Harga Satuan Kecil</span>
                                <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($item->harga_satuan_kecil ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Harga Satuan Besar</span>
                                <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($item->harga_satuan_besar ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <hr class="border-gray-200/50 dark:border-gray-700/50 my-1">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Total Nilai Aset (Kecil)</span>
                                <span class="font-extrabold text-primary-600 dark:text-primary-400">Rp {{ number_format(($item->stok_saat_ini_kecil ?? 0) * ($item->harga_satuan_kecil ?? 0), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penyimpanan & Anggaran</h4>
                        <div class="space-y-3 bg-gray-50 dark:bg-navy-800/40 p-5 rounded-2xl border border-gray-100/50 dark:border-gray-800 text-sm">
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-gray-500 shrink-0">Lokasi</span>
                                <span class="font-bold text-gray-900 dark:text-white text-right leading-tight">{{ optional($item->lokasiBarang)->nama_lokasi ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-gray-500 shrink-0">Sumber Dana</span>
                                <span class="font-bold text-gray-900 dark:text-white text-right leading-tight">{{ optional($item->sumberAnggaran)->nama_sumber ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Min Stok (Warning)</span>
                                <span class="font-bold px-2 py-0.5 bg-yellow-50 dark:bg-yellow-950/30 text-yellow-600 dark:text-yellow-400 rounded-md text-xs">{{ $item->stok_minimal ?? 0 }} {{ optional($item->satuanKecil)->nama_satuan }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                    
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Masa Berlaku & Tanggal</h4>
                        <div class="space-y-3 bg-gray-50 dark:bg-navy-800/40 p-5 rounded-2xl border border-gray-100/50 dark:border-gray-800 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Tanggal Diterima</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $item->tgl_diterima ? $item->tgl_diterima->format('d F Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Tanggal Expired</span>
                                <span class="font-bold text-gray-900 dark:text-white">
                                    @if($item->tgl_kadaluarsa)
                                        <span class="{{ $item->tgl_kadaluarsa < \Carbon\Carbon::today() ? 'text-red-600 font-extrabold' : ($item->tgl_kadaluarsa <= \Carbon\Carbon::today()->addDays(30) ? 'text-yellow-600 font-bold' : 'text-gray-900 dark:text-white') }}">
                                            {{ $item->tgl_kadaluarsa->format('d F Y') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Deskripsi & Catatan</h4>
                        <div class="bg-gray-50 dark:bg-navy-800/40 p-5 rounded-2xl border border-gray-100/50 dark:border-gray-800 text-sm min-h-[92px]">
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $item->deskripsi ?? 'Tidak ada deskripsi tambahan.' }}</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- Right: Transaction Timeline -->
        <div class="space-y-6">
            
            <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">Riwayat Mutasi</h3>
                    <span class="text-xs bg-gray-100 dark:bg-navy-800 text-gray-500 dark:text-gray-400 font-bold px-2.5 py-1 rounded-full">
                        {{ $item->transactions->count() }} Transaksi
                    </span>
                </div>

                <hr class="border-gray-100 dark:border-gray-800">

                <div class="relative pl-6 border-l-2 border-gray-100 dark:border-gray-800 space-y-8 max-h-[600px] overflow-y-auto pr-2 sidebar-scroll">
                    @forelse($item->transactions()->with(['gudang', 'pengguna'])->latest()->take(20)->get() as $tx)
                        <div class="relative">
                            <!-- Bullet marker -->
                            <div class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white dark:border-navy-900 flex items-center justify-center shadow-sm
                                {{ $tx->jenis === 'masuk' ? 'bg-green-500' : ($tx->jenis === 'keluar' ? 'bg-red-500' : 'bg-yellow-500') }}">
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex justify-between items-start gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $tx->jenis === 'masuk' ? 'bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400' : ($tx->jenis === 'keluar' ? 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400' : 'bg-yellow-50 dark:bg-yellow-950/30 text-yellow-700 dark:text-yellow-400') }}">
                                        {{ $tx->jenis }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium">
                                        {{ $tx->tgl_transaksi ? $tx->tgl_transaksi->format('d/m/y H:i') : '' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $tx->jenis === 'masuk' ? '+' : '-' }} {{ $tx->jumlah_barang_kecil }} {{ optional($item->satuanKecil)->nama_satuan }}
                                </h4>
                                @if($tx->jumlah_barang_besar > 0)
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                        ({{ $tx->jenis === 'masuk' ? '+' : '-' }} {{ $tx->jumlah_barang_besar }} {{ optional($item->satuanBesar)->nama_satuan }})
                                    </p>
                                @endif
                                <div class="space-y-0.5 text-[10px] text-gray-500 dark:text-gray-400">
                                    <p><span class="font-medium">Ref:</span> <span class="font-mono">{{ $tx->no_referensi }}</span></p>
                                    <p><span class="font-medium">Gudang:</span> {{ optional($tx->gudang)->nama_gudang ?? '-' }}</p>
                                    <p><span class="font-medium">Operator:</span> {{ optional($tx->pengguna)->nama_lengkap ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-clock-rotate-left text-gray-300 dark:text-gray-700 text-3xl mb-2"></i>
                            <p class="text-xs text-gray-400 font-medium">Belum ada riwayat mutasi.</p>
                        </div>
                    @endforelse
                </div>

            </div>

        </div>

    </div>
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
