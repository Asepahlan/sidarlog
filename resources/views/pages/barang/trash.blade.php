@extends('layouts.app')

@section('title', 'Barang Terhapus')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Trash Barang</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Daftar barang yang telah dihapus (Soft Delete). Anda dapat mengembalikan atau menghapus permanen.</p>
        </div>
        <a href="{{ route('barang.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-navy-900 border border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300 font-bold rounded-xl transition-all shadow-sm hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

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
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Informasi Barang</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Dihapus Pada</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-6 py-5 text-sm text-gray-500 text-center">{{ $index + 1 }}</td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->nama_barang }}</span>
                                <span class="text-xs text-gray-500 font-mono">{{ $item->kode_barang }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-600 dark:text-gray-400">
                            {{ $item->deleted_at->format('d/m/Y H:i') }}
                            <span class="text-xs block text-gray-400">{{ $item->deleted_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-5 text-center space-x-3">
                            <form action="{{ route('barang.restore', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 font-bold text-xs flex items-center inline-flex" title="Restore">
                                    <i class="fas fa-undo mr-1"></i> Restore
                                </button>
                            </form>
                            <form id="force-delete-form-{{ $item->id }}" action="{{ route('barang.force-delete', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('force-delete-form-{{ $item->id }}', 'Hapus Permanen?', 'Apakah Anda yakin ingin menghapus barang ' + '{{ $item->nama_barang }}' + ' secara permanen? Data yang sudah dihapus permanen tidak dapat dikembalikan lagi.')" class="text-red-600 hover:text-red-800 font-bold text-xs flex items-center inline-flex" title="Hapus Permanen">
                                    <i class="fas fa-eraser mr-1"></i> Hapus Permanen
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i class="fas fa-trash-can text-4xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Trash kosong.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
