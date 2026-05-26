@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Pengaturan Sistem</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Konfigurasi global aplikasi SIDARLOG.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="space-y-8">
        @role('super_admin')
        <!-- Global App Settings (Super Admin Only) -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/50 flex items-center">
                <i class="fas fa-tools mr-3 text-primary-500"></i>
                <h3 class="font-bold text-gray-900 dark:text-white">Konfigurasi Aplikasi Global</h3>
            </div>
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Aplikasi</label>
                            <input type="text" name="app_name" value="{{ \App\Models\Setting::get('app_name') }}" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Aplikasi</label>
                            <textarea name="app_description" rows="3" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">{{ \App\Models\Setting::get('app_description') }}</textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6">
                        <div class="flex items-start space-x-4">
                            <div class="shrink-0">
                                <p class="text-xs font-bold text-gray-500 mb-2">Logo Utama</p>
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-900 rounded-2xl flex items-center justify-center border border-dashed border-gray-300 dark:border-gray-700">
                                    @if(\App\Models\Setting::get('app_logo'))
                                        <img src="{{ asset(\App\Models\Setting::get('app_logo')) }}" class="max-h-full max-w-full">
                                    @else
                                        <i class="fas fa-image text-gray-400"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Upload Logo</label>
                                <input type="file" name="app_logo" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="shrink-0">
                                <p class="text-xs font-bold text-gray-500 mb-2">Favicon</p>
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-900 rounded-xl flex items-center justify-center border border-dashed border-gray-300 dark:border-gray-700">
                                    @if(\App\Models\Setting::get('app_favicon'))
                                        <img src="{{ \App\Models\Setting::get('app_favicon') }}" class="max-h-full max-w-full">
                                    @else
                                        <i class="fas fa-icons text-gray-400"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Upload Favicon</label>
                                <input type="file" name="app_favicon" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition-all">
                        Update Konfigurasi Global
                    </button>
                </div>
            </form>
        </div>
        @endrole

        <!-- App Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/50 flex items-center">
                <i class="fas fa-desktop mr-3 text-primary-500"></i>
                <h3 class="font-bold text-gray-900 dark:text-white">Preferensi Tampilan</h3>
            </div>
            <div class="p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Mode Gelap (Dark Mode)</h4>
                        <p class="text-xs text-gray-500 mt-1">Gunakan tema gelap untuk mengurangi kelelahan mata.</p>
                    </div>
                    <button @click="darkMode = !darkMode" class="w-12 h-6 rounded-full transition-colors relative" :class="darkMode ? 'bg-primary-600' : 'bg-gray-200'">
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform" :class="darkMode ? 'translate-x-6' : ''"></div>
                    </button>
                </div>
                <div class="border-t border-gray-50 dark:border-gray-700 pt-6 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Sidebar Ciut (Collapsed Sidebar)</h4>
                        <p class="text-xs text-gray-500 mt-1">Mengecilkan sidebar secara default untuk area kerja lebih luas.</p>
                    </div>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="w-12 h-6 rounded-full transition-colors relative" :class="sidebarCollapsed ? 'bg-primary-600' : 'bg-gray-200'">
                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform" :class="sidebarCollapsed ? 'translate-x-6' : ''"></div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Localization Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/50 flex items-center">
                <i class="fas fa-globe mr-3 text-primary-500"></i>
                <h3 class="font-bold text-gray-900 dark:text-white">Lokalisasi & Waktu</h3>
            </div>
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Zona Waktu</label>
                        <select disabled class="w-full px-4 py-3 border rounded-xl bg-gray-50 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 outline-none opacity-60">
                            <option>Asia/Jakarta (WIB)</option>
                        </select>
                        <p class="text-[10px] text-gray-400 mt-2">Dikonfigurasi melalui file sistem.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Bahasa Sistem</label>
                        <select disabled class="w-full px-4 py-3 border rounded-xl bg-gray-50 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 outline-none opacity-60">
                            <option>Bahasa Indonesia</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-gray-900 p-8 rounded-3xl border border-navy-800 shadow-xl">
            <h3 class="text-white font-bold mb-4">Informasi Versi</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Aplikasi</p>
                    <p class="text-sm text-gray-300">SIDARLOG v2.0.4-enterprise</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Platform</p>
                    <p class="text-sm text-gray-300">Laravel v11.x (PHP v8.2)</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
