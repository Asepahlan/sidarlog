@extends('layouts.app')

@section('title', 'Barang ' . ucfirst($jenis))

@section('content')
<div class="space-y-6" x-data="{ openCreate: false }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Barang {{ ucfirst($jenis) }}</h1>
            <p class="text-gray-500 dark:text-gray-400">Riwayat transaksi barang {{ $jenis }} sistem.</p>
        </div>
        <a href="{{ route('barang-' . $jenis . '.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/30">
            <i class="fas fa-plus mr-2"></i> Tambah Transaksi
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3" role="alert">
        <i class="fas fa-check-circle text-lg"></i>
        <span class="block sm:inline font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">No. Ref</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Barang</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Gudang</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Jumlah (Kcl)</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Jumlah (Bsr)</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Penerima/Penyerah</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ $tx->no_referensi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $tx->barang->nama_barang ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $tx->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $tx->jenis == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $tx->jenis == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $tx->jenis == 'masuk' ? '+' : '-' }}{{ number_format($tx->jumlah_barang_kecil) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $tx->jenis == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $tx->jenis == 'masuk' ? '+' : '-' }}{{ number_format($tx->jumlah_barang_besar) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $tx->penerima_penyerah ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $tx->tgl_transaksi ? $tx->tgl_transaksi->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center space-x-2">
                            <a href="{{ route('transaksi.bast', $tx->id) }}" target="_blank" class="text-primary-600 hover:text-primary-800 transition-colors" title="Cetak Berita Acara">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <form id="delete-form-{{ $tx->id }}" action="{{ route('barang-' . $tx->jenis . '.destroy', $tx->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('delete-form-{{ $tx->id }}', 'Batalkan Transaksi?', 'Apakah Anda yakin ingin membatalkan transaksi ' + '{{ $tx->no_referensi }}' + '? Stok barang akan dikembalikan secara otomatis ke kondisi sebelumnya.')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i class="fas fa-exchange-alt text-4xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Data transaksi kosong</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Modal Create Transaction -->
    <x-modal :title="'Tambah Transaksi ' . ucfirst($jenis ?? 'Masuk')" x-show="openCreate">
        <form action="{{ route('barang-' . ($jenis ?? 'masuk') . '.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="jenis" value="{{ $jenis ?? 'masuk' }}">
            <div class="space-y-5">
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Barang</label>
                    <select name="barang_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        <option value="">Pilih Barang</option>
                        @foreach(\App\Models\Item::all() as $item)
                            <option value="{{ $item->id }}">{{ $item->kode_barang }} - {{ $item->nama_barang }} (Stok: {{ $item->current_stock_kecil }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Gudang</label>
                    <select name="gudang_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                        @foreach(\App\Models\Warehouse::all() as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Jumlah (Kecil)</label>
                        <input type="number" name="jumlah_barang_kecil" value="0" min="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Jumlah (Besar)</label>
                        <input type="number" name="jumlah_barang_besar" value="0" min="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tanggal</label>
                    <input type="datetime-local" name="tgl_transaksi" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">{{ ($jenis ?? 'masuk') == 'masuk' ? 'Pengirim' : 'Penerima' }}</label>
                    <input type="text" name="penerima_penyerah" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none"></textarea>
                </div>
            </div>
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openCreate = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Simpan Transaksi</button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
