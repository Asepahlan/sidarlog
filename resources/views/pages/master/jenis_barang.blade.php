@extends('layouts.app')

@section('title', 'Jenis Barang')

@section('content')
<div class="space-y-6" x-data="{ openCreate: false, openEdit: false, editItem: {} }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Jenis Barang</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Sub-kategori barang untuk pengelompokan yang lebih detail.</p>
        </div>
        <button @click="openCreate = true" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/30">
            <i class="fas fa-plus mr-2"></i> Tambah Jenis
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-navy-800/50">
                    <tr>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kategori Utama</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Jenis Barang</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Deskripsi</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($types as $type)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-4 py-5">
                            <span class="px-2 py-1 bg-primary-50 text-primary-600 text-[10px] font-bold rounded uppercase">{{ $type->kategori->nama_kategori }}</span>
                        </td>
                        <td class="px-4 py-5 text-sm font-bold text-gray-900 dark:text-white">{{ $type->nama_jenis }}</td>
                        <td class="px-4 py-5 text-sm text-gray-500">{{ $type->deskripsi ?? '-' }}</td>
                        <td class="px-4 py-5 text-right space-x-2">
                            <button @click="editItem = {{ $type->toJson() }}; openEdit = true" class="text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form id="delete-form-{{ $type->id }}" action="{{ route('jenis-barang.destroy', $type->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('delete-form-{{ $type->id }}', 'Hapus Jenis Barang?', 'Apakah Anda yakin ingin menghapus ' + '{{ $type->nama_jenis }}' + '?')" class="text-red-600 hover:text-red-800 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-gray-400 italic text-sm">Data jenis barang belum tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create -->
    <x-modal title="Tambah Jenis Barang" x-show="openCreate">
        <form action="{{ route('jenis-barang.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Kategori Utama</label>
                <select name="kategori_id" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Jenis Barang</label>
                <input type="text" name="nama_jenis" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Contoh: Meja, Kursi, Laptop, dll.">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                <textarea name="deskripsi" rows="2" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none"></textarea>
            </div>
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openCreate = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Simpan Jenis</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit -->
    <x-modal title="Edit Jenis Barang" x-show="openEdit">
        <form :action="'/jenis-barang/' + editItem.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Kategori Utama</label>
                <select name="kategori_id" x-model="editItem.kategori_id" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Jenis Barang</label>
                <input type="text" name="nama_jenis" x-model="editItem.nama_jenis" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                <textarea name="deskripsi" x-model="editItem.deskripsi" rows="2" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none"></textarea>
            </div>
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openEdit = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
