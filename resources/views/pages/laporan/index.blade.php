@extends('layouts.app')

@section('title', 'Pusat Laporan')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pusat Laporan</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Export data inventory dan transaksi dalam format PDF atau Excel untuk keperluan audit dan pelaporan BPBD.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- ══ 1. MASTER BARANG ══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-boxes-stacked text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Master Barang</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Daftar semua barang yang terdaftar di sistem beserta stok saat ini.</p>
            </div>
            <div class="mt-6 flex gap-2">
                <a href="{{ route('laporan.barang.pdf') }}"
                   class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
                <a href="{{ route('laporan.barang.excel') }}"
                   class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-excel mr-1"></i> Excel
                </a>
            </div>
        </div>

        {{-- ══ 2. BARANG MASUK ══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between"
             x-data="{ open: false }">
            <div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-arrow-right-to-bracket text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Barang Masuk</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Rekapitulasi semua transaksi barang masuk ke gudang.</p>

                {{-- Filter tanggal --}}
                <div x-show="open" x-collapse class="mt-3 space-y-2">
                    <form id="form-masuk" method="GET" class="space-y-2">
                        <input type="hidden" name="jenis" value="masuk">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Dari Tanggal</label>
                            <input type="date" name="start_date"
                                   class="w-full mt-1 px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Sampai Tanggal</label>
                            <input type="date" name="end_date"
                                   class="w-full mt-1 px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                <button @click="open = !open"
                        class="w-full py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 border border-dashed border-gray-300 dark:border-gray-600 rounded-xl transition-colors">
                    <i class="fas fa-filter mr-1"></i>
                    <span x-text="open ? 'Sembunyikan Filter' : 'Filter Tanggal'"></span>
                </button>
                <div class="flex gap-2">
                    <button type="button"
                            onclick="submitForm('form-masuk', '{{ route('laporan.transaksi.pdf') }}')"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                    <button type="button"
                            onclick="submitForm('form-masuk', '{{ route('laporan.transaksi.excel') }}')"
                            class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>
                </div>
            </div>
        </div>

        {{-- ══ 3. BARANG KELUAR ══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between"
             x-data="{ open: false }">
            <div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-arrow-right-from-bracket text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Barang Keluar</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Rekapitulasi semua transaksi barang keluar / distribusi.</p>

                {{-- Filter tanggal --}}
                <div x-show="open" x-collapse class="mt-3 space-y-2">
                    <form id="form-keluar" method="GET" class="space-y-2">
                        <input type="hidden" name="jenis" value="keluar">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Dari Tanggal</label>
                            <input type="date" name="start_date"
                                   class="w-full mt-1 px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Sampai Tanggal</label>
                            <input type="date" name="end_date"
                                   class="w-full mt-1 px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500">
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                <button @click="open = !open"
                        class="w-full py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 border border-dashed border-gray-300 dark:border-gray-600 rounded-xl transition-colors">
                    <i class="fas fa-filter mr-1"></i>
                    <span x-text="open ? 'Sembunyikan Filter' : 'Filter Tanggal'"></span>
                </button>
                <div class="flex gap-2">
                    <button type="button"
                            onclick="submitForm('form-keluar', '{{ route('laporan.transaksi.pdf') }}')"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                    <button type="button"
                            onclick="submitForm('form-keluar', '{{ route('laporan.transaksi.excel') }}')"
                            class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>
                </div>
            </div>
        </div>

        {{-- ══ 4. MUTASI GUDANG ══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-shuffle text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Mutasi Gudang</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Daftar perpindahan stok antar lokasi gudang.</p>
            </div>
            <div class="mt-6 flex gap-2">
                <a href="{{ route('laporan.mutasi.pdf') }}"
                   class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
                <a href="{{ route('laporan.mutasi.excel') }}"
                   class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-excel mr-1"></i> Excel
                </a>
            </div>
        </div>

        {{-- ══ 5. STOCK OPNAME ══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Stock Opname</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Rekap audit kecocokan stok sistem vs fisik di gudang.</p>
            </div>
            <div class="mt-6 flex gap-2">
                <a href="{{ route('laporan.opname.pdf') }}"
                   class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
                <a href="{{ route('laporan.opname.excel') }}"
                   class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-excel mr-1"></i> Excel
                </a>
            </div>
        </div>

        {{-- ══ 6. SEMUA TRANSAKSI ══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-list-check text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Semua Transaksi</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Export seluruh transaksi (masuk + keluar) tanpa filter jenis.</p>
            </div>
            <div class="mt-6 flex gap-2">
                <a href="{{ route('laporan.transaksi.pdf') }}"
                   class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
                <a href="{{ route('laporan.transaksi.excel') }}"
                   class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-excel mr-1"></i> Excel
                </a>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
/**
 * Submit form laporan dengan action URL yang dinamis
 * Digunakan untuk kartu laporan yang punya filter tanggal
 */
function submitForm(formId, actionUrl) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.action = actionUrl;
    form.submit();
}
</script>
@endpush
@endsection
