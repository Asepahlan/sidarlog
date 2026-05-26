@extends('layouts.app')

@section('title', 'Master Satuan')

@section('content')
<div class="space-y-6" x-data="{ openCreate: false, openEdit: false, editItem: {} }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Satuan Barang</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola unit pengukuran barang (Pcs, Box, Rim, dll).</p>
        </div>
        <button @click="openCreate = true" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/30">
            <i class="fas fa-plus mr-2"></i> Tambah Satuan
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
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Nama Satuan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-left">Simbol / Singkatan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($units as $index => $unit)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ $unit->nama_satuan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $unit->simbol ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <button @click="editItem = {{ $unit->toJson() }}; openEdit = true" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form id="delete-form-{{ $unit->id }}" action="{{ route('satuan.destroy', $unit->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('delete-form-{{ $unit->id }}', 'Hapus Satuan?', 'Apakah Anda yakin ingin menghapus satuan ' + '{{ $unit->nama_satuan }}' + '?')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i class="fas fa-balance-scale text-4xl text-gray-300 dark:text-gray-600"></i>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Data satuan belum tersedia.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create -->
    <x-modal title="Tambah Satuan Baru" x-show="openCreate">
        <form action="{{ route('satuan.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-5">
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nama Satuan</label>
                    <input type="text" name="nama_satuan" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none" placeholder="Contoh: Buah, Rim, Pack">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Simbol / Singkatan</label>
                    <input type="text" name="simbol" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none" placeholder="Contoh: Pcs, Box, Set">
                </div>
            </div>
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openCreate = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Simpan Satuan</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit -->
    <x-modal title="Edit Data Satuan" x-show="openEdit">
        <form :action="'/satuan/' + editItem.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nama Satuan</label>
                    <input type="text" name="nama_satuan" x-model="editItem.nama_satuan" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Simbol / Singkatan</label>
                    <input type="text" name="simbol" x-model="editItem.simbol" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                </div>
            </div>
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openEdit = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
