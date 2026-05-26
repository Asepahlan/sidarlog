@extends('layouts.app')

@section('title', 'Manajemen Role & Hak Akses')

@section('content')
@php
$groupIcons = [
    'Dashboard' => 'fas fa-chart-pie',
    'Master Barang' => 'fas fa-boxes',
    'Master Data Pendukung' => 'fas fa-database',
    'Manajemen Gudang' => 'fas fa-warehouse',
    'Mutasi Barang' => 'fas fa-exchange-alt',
    'Transaksi (Masuk/Keluar)' => 'fas fa-file-invoice-dollar',
    'Stock Opname' => 'fas fa-clipboard-check',
    'Laporan' => 'fas fa-file-alt',
    'Pengaturan & Sistem' => 'fas fa-cogs'
];
@endphp

<div class="space-y-6" x-data="{ openCreate: false, openEdit: false, editItem: {}, createPermissions: [], editPermissions: [] }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Role & Hak Akses</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data role pengguna dan hak akses sistem.</p>
        </div>
        <button @click="createPermissions = []; openCreate = true" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/30">
            <i class="fas fa-plus mr-2"></i> Tambah Role
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

    <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden max-w-5xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-navy-800/50">
                    <tr>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Role</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Guard</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($roles as $r)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-4 py-5">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-primary-50 text-primary-600 text-xs font-bold rounded-lg uppercase tracking-wider">{{ $r->name }}</span>
                            </div>
                            <div class="mt-2 text-xs">
                                @if($r->name === 'super_admin')
                                    <span class="text-green-600 dark:text-green-400 font-semibold"><i class="fas fa-shield-alt mr-1"></i> Akses Penuh Sistem</span>
                                @else
                                    <div x-data="{ showDetail: false }" class="relative inline-block text-left">
                                        <button @click="showDetail = !showDetail" type="button" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 font-medium inline-flex items-center gap-1 transition-colors">
                                            <i class="fas fa-key mr-0.5 text-primary-500"></i> 
                                            <span>{{ $r->permissions->count() }} Hak Akses Aktif</span>
                                            <i class="fas text-[9px] transition-transform duration-200" :class="showDetail ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                        
                                        <!-- Dropdown Detail -->
                                        <div x-show="showDetail" 
                                             @click.away="showDetail = false"
                                             x-transition
                                             class="absolute left-0 mt-2 w-80 bg-white dark:bg-navy-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl p-4 z-20 space-y-2 max-h-60 overflow-y-auto">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b pb-1 dark:border-gray-700">Daftar Hak Akses ({{ $r->name }})</p>
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($r->permissions as $perm)
                                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-navy-900 text-gray-600 dark:text-gray-400 text-[9px] rounded font-mono">{{ $perm->name }}</span>
                                                @empty
                                                    <span class="text-[10px] text-gray-400 italic">Tidak ada hak akses</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-5 text-sm text-gray-500">{{ $r->guard_name }}</td>
                        <td class="px-4 py-5 text-right space-x-2 whitespace-nowrap">
                            <button @click="editItem = {{ $r->toJson() }}; editPermissions = editItem.permissions ? editItem.permissions.map(p => p.name) : []; openEdit = true" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($r->name !== 'super_admin')
                            <form id="delete-form-{{ $r->id }}" action="{{ route('roles.destroy', $r->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="triggerDelete('delete-form-{{ $r->id }}', 'Hapus Role?', 'Apakah Anda yakin ingin menghapus role ' + '{{ $r->name }}' + '? Seluruh hak akses user dengan role ini akan terpengaruh.')" class="text-red-600 hover:text-red-800 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center text-gray-400 italic text-sm">Data role belum tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create -->
    <x-modal title="Tambah Role Baru" x-show="openCreate" max-width="5xl">
        <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="bg-gray-50 dark:bg-navy-800/40 p-5 rounded-3xl border border-gray-100 dark:border-gray-800">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Nama Role</label>
                <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-2xl dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all" placeholder="Contoh: admin, staff, auditor">
            </div>

            <!-- Create Modal Permission Checklist -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pilih Hak Akses (Permissions)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Centang hak akses yang ingin diberikan pada role baru ini.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 max-h-[420px] overflow-y-auto pr-2">
                    @foreach($permissionGroups as $groupName => $groupPerms)
                    <div class="p-5 bg-gray-50/50 dark:bg-navy-800/30 rounded-3xl border border-gray-100 dark:border-gray-800/60 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200/60 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-primary-50 dark:bg-primary-950/20 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xs">
                                    <i class="{{ $groupIcons[$groupName] ?? 'fas fa-shield-alt' }}"></i>
                                </span>
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $groupName }}</span>
                            </div>
                            
                            <label class="inline-flex items-center text-[10px] text-primary-600 dark:text-primary-400 font-bold cursor-pointer select-none hover:underline">
                                <input type="checkbox" 
                                       :checked='@json($groupPerms).every(p => createPermissions.includes(p))'
                                       @change='
                                           const groupPerms = @json($groupPerms);
                                           if ($el.checked) {
                                               groupPerms.forEach(p => { if (!createPermissions.includes(p)) createPermissions.push(p) });
                                           } else {
                                               createPermissions = createPermissions.filter(p => !groupPerms.includes(p));
                                           }
                                       ' 
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 mr-1 w-3.5 h-3.5">
                                Pilih Semua
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                            @foreach($groupPerms as $perm)
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-2xl border transition-all cursor-pointer select-none hover:shadow-sm"
                                   :class="createPermissions.includes('{{ $perm }}') ? 'border-primary-500 bg-primary-50/30 dark:bg-primary-950/10 text-primary-900 dark:text-primary-100' : 'border-gray-200 dark:border-gray-800 bg-white dark:bg-navy-900 text-gray-700 dark:text-gray-300'">
                                <input type="checkbox" name="permissions[]" value="{{ $perm }}" x-model="createPermissions" class="rounded border-gray-300 dark:border-gray-700 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                <span class="text-[10px] font-mono leading-none">{{ $perm }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-gray-500 hover:text-gray-700 font-bold text-sm">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 text-sm">Simpan Role</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit -->
    <x-modal title="Edit Role & Hak Akses" x-show="openEdit" max-width="5xl">
        <form :action="'/roles/' + editItem.id" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="bg-gray-50 dark:bg-navy-800/40 p-5 rounded-3xl border border-gray-100 dark:border-gray-800">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Nama Role</label>
                <input type="text" name="name" x-model="editItem.name" :disabled="editItem.name === 'super_admin'" required class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-2xl dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none disabled:opacity-50 transition-all">
            </div>

            <!-- Edit Modal Permission Checklist -->
            <div class="space-y-4" x-show="editItem.name !== 'super_admin'">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pilih Hak Akses (Permissions)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Centang hak akses yang ingin diberikan pada role ini.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 max-h-[420px] overflow-y-auto pr-2">
                    @foreach($permissionGroups as $groupName => $groupPerms)
                    <div class="p-5 bg-gray-50/50 dark:bg-navy-800/30 rounded-3xl border border-gray-100 dark:border-gray-800/60 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200/60 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-primary-50 dark:bg-primary-950/20 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xs">
                                    <i class="{{ $groupIcons[$groupName] ?? 'fas fa-shield-alt' }}"></i>
                                </span>
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $groupName }}</span>
                            </div>
                            
                            <label class="inline-flex items-center text-[10px] text-primary-600 dark:text-primary-400 font-bold cursor-pointer select-none hover:underline">
                                <input type="checkbox" 
                                       :checked='@json($groupPerms).every(p => editPermissions.includes(p))'
                                       @change='
                                           const groupPerms = @json($groupPerms);
                                           if ($el.checked) {
                                               groupPerms.forEach(p => { if (!editPermissions.includes(p)) editPermissions.push(p) });
                                           } else {
                                               editPermissions = editPermissions.filter(p => !groupPerms.includes(p));
                                           }
                                       ' 
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 mr-1 w-3.5 h-3.5">
                                Pilih Semua
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                            @foreach($groupPerms as $perm)
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-2xl border transition-all cursor-pointer select-none hover:shadow-sm"
                                   :class="editPermissions.includes('{{ $perm }}') ? 'border-primary-500 bg-primary-50/30 dark:bg-primary-950/10 text-primary-900 dark:text-primary-100' : 'border-gray-200 dark:border-gray-800 bg-white dark:bg-navy-900 text-gray-700 dark:text-gray-300'">
                                <input type="checkbox" name="permissions[]" value="{{ $perm }}" x-model="editPermissions" class="rounded border-gray-300 dark:border-gray-700 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                <span class="text-[10px] font-mono leading-none">{{ $perm }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-950/20 text-yellow-800 dark:text-yellow-400 text-xs rounded-2xl border border-yellow-100 dark:border-yellow-900/50" x-show="editItem.name === 'super_admin'">
                <i class="fas fa-info-circle mr-1"></i> Hak akses Super Admin tidak dapat diubah dari web interface untuk mencegah penguncian sistem.
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openEdit = false" class="px-5 py-2.5 text-gray-500 hover:text-gray-700 font-bold text-sm">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 text-sm">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
