@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pusat Laporan</h1>
        <p class="text-gray-500 dark:text-gray-400">Export data inventory dan transaksi dalam format PDF atau Excel.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Master Barang Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-boxes-stacked text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Master Barang</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Daftar semua barang yang terdaftar di sistem beserta stok saat ini.</p>
            </div>
            <div class="mt-6 flex gap-2">
                <a href="{{ route('laporan.barang.pdf') }}" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
                <a href="{{ route('laporan.barang.excel') }}" class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-excel mr-1"></i> Excel
                </a>
            </div>
        </div>

        <!-- Barang Masuk Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-arrow-right-to-bracket text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Barang Masuk</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Rekapitulasi semua transaksi barang masuk ke gudang.</p>
            </div>
            <div class="mt-6">
                <a href="{{ route('laporan.transaksi.pdf') }}?jenis=masuk" class="block w-full py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
            </div>
        </div>

        <!-- Barang Keluar Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-arrow-right-from-bracket text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Laporan Barang Keluar</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Rekapitulasi semua transaksi barang keluar / distribusi.</p>
            </div>
            <div class="mt-6">
                <a href="{{ route('laporan.transaksi.pdf') }}?jenis=keluar" class="block w-full py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
