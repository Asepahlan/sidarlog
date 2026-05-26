@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6" x-data="{ openCreate: false, openEdit: false, editItem: {}, editRole: '' }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Manajemen User</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola akun pengguna dan hak akses sistem.</p>
        </div>
        <button @click="openCreate = true" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/30">
            <i class="fas fa-user-plus mr-2"></i> Tambah User
        </button>
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
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-navy-800/50">
                    <tr>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">User</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">NIP / Jabatan</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-4 py-5">
                            <div class="flex items-center">
                                <img src="{{ $user->foto ?? 'https://ui-avatars.com/api/?name='.$user->nama_lengkap.'&background=0284c7&color=fff' }}" class="w-10 h-10 rounded-xl mr-3 border border-gray-100 dark:border-gray-800">
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->nama_lengkap }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-5">
                            <p class="text-sm font-bold text-primary-600">{{ $user->nip }}</p>
                            <p class="text-[10px] text-gray-400 uppercase tracking-tight">{{ $user->jabatan->nama_jabatan ?? 'N/A' }}</p>
                        </td>
                        <td class="px-4 py-5">
                            @foreach($user->roles as $role)
                                <span class="px-2 py-1 bg-gray-100 dark:bg-navy-800 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded uppercase mr-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-5">
                            <span class="inline-flex items-center px-2 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded uppercase">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Aktif
                            </span>
                        </td>
                        <td class="px-4 py-5 text-right space-x-2">
                            <button @click="editItem = {{ $user->toJson() }}; editRole = '{{ $user->roles->first()->name ?? '' }}'; openEdit = true" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('delete-form-{{ $user->id }}', 'Hapus Akun User?', 'Apakah Anda yakin ingin menghapus akun ' + '{{ $user->nama_lengkap }}' + '? Pengguna ini tidak akan bisa login lagi.')" class="text-red-600 hover:text-red-800 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create -->
    <x-modal title="Tambah User Baru" x-show="openCreate">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">NIP</label>
                    <input type="text" name="nip" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-xs font-normal text-gray-400">(Opsional)</span></label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Kosongkan jika tidak ada">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Role Akses</label>
                    <select name="role" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                    <select name="jabatan_id" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Jabatan</option>
                        @foreach($jabatans as $j)
                            <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Bidang</label>
                    <select name="bidang_id" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Bidang</option>
                        @foreach($bidangs as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openCreate = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30">Simpan User</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit -->
    <x-modal title="Edit Data User" x-show="openEdit">
        <form :action="'/users/' + editItem.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" x-model="editItem.nama_lengkap" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-xs font-normal text-gray-400">(Opsional)</span></label>
                <input type="email" name="email" x-model="editItem.email" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Kosongkan jika tidak ada">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Role Akses</label>
                    <select name="role" x-model="editRole" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Ganti Sandi (Opsional)</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Kosongkan jika tidak ganti">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                    <select name="jabatan_id" x-model="editItem.jabatan_id" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Jabatan</option>
                        @foreach($jabatans as $j)
                            <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Bidang</label>
                    <select name="bidang_id" x-model="editItem.bidang_id" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Bidang</option>
                        @foreach($bidangs as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
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
