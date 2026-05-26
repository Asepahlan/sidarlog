@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Pengaturan Profil</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola informasi akun dan keamanan Anda.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-6 text-center">
                <div class="relative inline-block mb-4">
                    <img src="{{ $user->foto ?? 'https://ui-avatars.com/api/?name='.$user->nama_lengkap.'&background=0284c7&color=fff' }}" class="w-32 h-32 rounded-3xl mx-auto border-4 border-gray-50 dark:border-gray-900 shadow-xl">
                    <button class="absolute -bottom-2 -right-2 w-10 h-10 bg-primary-600 text-white rounded-xl shadow-lg border-2 border-white dark:border-gray-800 flex items-center justify-center hover:bg-primary-700 transition-all">
                        <i class="fas fa-camera text-sm"></i>
                    </button>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->nama_lengkap }}</h2>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-1">{{ $user->getRoleNames()->first() ?? 'User' }}</p>
                <div class="mt-6 pt-6 border-t border-gray-50 dark:border-gray-700 space-y-4 text-left">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-id-card w-6 text-primary-500"></i>
                        <span>{{ $user->nip ?? 'NIP Belum Diatur' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-envelope w-6 text-primary-500"></i>
                        <span>{{ $user->email ?? 'Belum diatur' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forms -->
        <div class="md:col-span-2 space-y-8">
            <!-- Basic Info -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/50">
                    <h3 class="font-bold text-gray-900 dark:text-white">Informasi Dasar</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email <span class="text-xs font-normal text-gray-400">(Opsional)</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Kosongkan jika tidak ada">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Info -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/50">
                    <h3 class="font-bold text-gray-900 dark:text-white">Ubah Kata Sandi</h3>
                </div>
                <form action="{{ route('profile.password') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kata Sandi Baru</label>
                            <input type="password" name="password" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
