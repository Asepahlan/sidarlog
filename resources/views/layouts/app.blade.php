<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::get('app_name', config('app.name', 'SIDARLOG')) }} - @yield('title')</title>
    @if(\App\Models\Setting::get('app_favicon'))
        <link rel="icon" type="image/png" href="{{ asset(\App\Models\Setting::get('app_favicon')) }}">
    @endif

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7', // SIDARLOG Blue
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a', // Dark Navy
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
</head>
<body class="h-full overflow-hidden bg-gray-50 dark:bg-gray-950" 
      x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        darkMode: localStorage.getItem('darkMode') === 'true',
        confirmDelete: {
            open: false,
            title: 'Hapus Data?',
            message: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
            formId: null
        },
        triggerDelete(formId, title = 'Hapus Data?', message = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.') {
            this.confirmDelete.formId = formId;
            this.confirmDelete.title = title;
            this.confirmDelete.message = message;
            this.confirmDelete.open = true;
        },
        executeDelete() {
            if(this.confirmDelete.formId) {
                document.getElementById(this.confirmDelete.formId).submit();
            }
        }
      }" 
      :class="{ 'dark': darkMode }" 
      x-init="$watch('sidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside 
            :class="sidebarCollapsed ? 'w-20' : 'w-72'"
            class="hidden md:flex flex-col flex-shrink-0 transition-all duration-300 ease-in-out bg-navy-950 text-gray-300 border-r border-gray-800"
        >
            <!-- Sidebar Header -->
            <div class="flex items-center h-16 px-6 bg-navy-950 border-b border-gray-800 overflow-hidden shrink-0">
                <div class="flex items-center min-w-max">
                    <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20 overflow-hidden">
                        @if(\App\Models\Setting::get('app_logo'))
                            <img src="{{ asset(\App\Models\Setting::get('app_logo')) }}" class="max-h-full max-w-full">
                        @else
                            <i class="fas fa-boxes-stacked text-white text-xl"></i>
                        @endif
                    </div>
                    <span class="ml-4 text-white font-bold text-xl tracking-wider transition-opacity duration-300" 
                          :class="sidebarCollapsed ? 'opacity-0 invisible' : 'opacity-100 visible'">
                        {{ \App\Models\Setting::get('app_name', 'SIDARLOG') }}
                    </span>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <div class="flex-1 flex flex-col overflow-y-auto sidebar-scroll py-4 px-3">
                
                <!-- Main Section -->
                @can('dashboard.view')
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-[2px] mb-3" :class="sidebarCollapsed ? 'hidden' : 'block'">Utama</p>
                    <x-sidebar-item title="Dashboard" href="/dashboard" icon="fas fa-grid-2" :active="request()->is('dashboard')" />
                </div>
                @endcan

                <!-- Master Data -->
                @canany(['barang.view', 'master.kategori', 'master.satuan', 'master.lokasi', 'gudang.view', 'mutasi.view'])
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-[2px] mb-3" :class="sidebarCollapsed ? 'hidden' : 'block'">Master Data</p>
                    
                    @can('barang.view')
                    <x-sidebar-dropdown title="Barang" icon="fas fa-box" :active="request()->is('barang') || request()->is('barang/*') || request()->is('lokasi-barang*') || request()->is('satuan*') || request()->is('sumber-anggaran*') || request()->is('pihak-kesatu*') || request()->is('pihak-kedua*') || request()->is('bap*')">
                        <x-sidebar-submenu-item title="Data Barang" href="/barang" :active="request()->is('barang') || request()->is('barang/*')" />
                        @can('master.lokasi')
                        <x-sidebar-submenu-item title="Data Lokasi Barang" href="/lokasi-barang" :active="request()->is('lokasi-barang*')" />
                        @endcan
                        @can('master.satuan')
                        <x-sidebar-submenu-item title="Data Satuan Barang" href="/satuan" :active="request()->is('satuan*')" />
                        @endcan
                        @can('master.sumber-anggaran')
                        <x-sidebar-submenu-item title="Data Sumber Anggaran" href="/sumber-anggaran" :active="request()->is('sumber-anggaran*')" />
                        @endcan
                        @can('master.pihak-kesatu')
                        <x-sidebar-submenu-item title="Referensi Pihak Kesatu" href="/pihak-kesatu" :active="request()->is('pihak-kesatu*')" />
                        @endcan
                        @can('master.pihak-kedua')
                        <x-sidebar-submenu-item title="Referensi Pihak Kedua" href="/pihak-kedua" :active="request()->is('pihak-kedua*')" />
                        @endcan
                        @can('master.bap')
                        <x-sidebar-submenu-item title="Referensi No. BAP" href="/bap" :active="request()->is('bap*')" />
                        @endcan
                    </x-sidebar-dropdown>
                    @endcan

                    @canany(['gudang.view', 'mutasi.view'])
                    <x-sidebar-dropdown title="Gudang" icon="fas fa-warehouse" :active="request()->is('gudang*') || request()->is('mutasi-gudang*')">
                        @can('gudang.view')
                        <x-sidebar-submenu-item title="Data Gudang" href="/gudang" :active="request()->is('gudang*')" />
                        @endcan
                        @can('mutasi.view')
                        <x-sidebar-submenu-item title="Mutasi Gudang" href="/mutasi-gudang" :active="request()->is('mutasi-gudang*')" />
                        @endcan
                    </x-sidebar-dropdown>
                    @endcanany

                    @canany(['master.kategori', 'master.jenis-barang', 'master.klasifikasi-barang'])
                    <x-sidebar-dropdown title="Kategori" icon="fas fa-tags" :active="request()->is('kategori*') || request()->is('jenis-barang*') || request()->is('klasifikasi-barang*')">
                        @can('master.kategori')
                        <x-sidebar-submenu-item title="Kategori Barang" href="/kategori" :active="request()->is('kategori*')" />
                        @endcan
                        @can('master.jenis-barang')
                        <x-sidebar-submenu-item title="Jenis Barang" href="/jenis-barang" :active="request()->is('jenis-barang*')" />
                        @endcan
                        @can('master.klasifikasi-barang')
                        <x-sidebar-submenu-item title="Klasifikasi Barang" href="/klasifikasi-barang" :active="request()->is('klasifikasi-barang*')" />
                        @endcan
                    </x-sidebar-dropdown>
                    @endcanany
                </div>
                @endcanany

                <!-- Inventory -->
                @canany(['transaksi.masuk.view', 'transaksi.keluar.view', 'opname.view'])
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-[2px] mb-3" :class="sidebarCollapsed ? 'hidden' : 'block'">Inventory</p>
                    
                    @can('transaksi.masuk.view')
                    <x-sidebar-dropdown title="Barang Masuk" icon="fas fa-arrow-right-to-bracket" :active="request()->is('barang-masuk*')">
                        @can('transaksi.masuk.create')
                        <x-sidebar-submenu-item title="Transaksi Barang Masuk" href="/barang-masuk/create" :active="request()->is('barang-masuk/create')" />
                        @endcan
                        <x-sidebar-submenu-item title="Riwayat Barang Masuk" href="/barang-masuk" :active="request()->is('barang-masuk')" />
                        @can('laporan.export')
                        <x-sidebar-submenu-item title="Export Barang Masuk" href="{{ route('laporan.transaksi.pdf', ['jenis' => 'masuk']) }}" />
                        @endcan
                    </x-sidebar-dropdown>
                    @endcan

                    @can('transaksi.keluar.view')
                    <x-sidebar-dropdown title="Barang Keluar" icon="fas fa-arrow-right-from-bracket" :active="request()->is('barang-keluar*')">
                        @can('transaksi.keluar.create')
                        <x-sidebar-submenu-item title="Transaksi Barang Keluar" href="/barang-keluar/create" :active="request()->is('barang-keluar/create')" />
                        @endcan
                        <x-sidebar-submenu-item title="Riwayat Barang Keluar" href="/barang-keluar" :active="request()->is('barang-keluar')" />
                        @can('laporan.export')
                        <x-sidebar-submenu-item title="Export Barang Keluar" href="{{ route('laporan.transaksi.pdf', ['jenis' => 'keluar']) }}" />
                        @endcan
                    </x-sidebar-dropdown>
                    @endcan

                    @can('opname.view')
                    <x-sidebar-dropdown title="Stock Opname" icon="fas fa-clipboard-check" :active="request()->is('stock-opname*')">
                        <x-sidebar-submenu-item title="Stock Opname" href="/stock-opname" :active="request()->is('stock-opname*')" />
                    </x-sidebar-dropdown>
                    @endcan
                </div>
                @endcanany

                <!-- Sistem -->
                @canany(['laporan.view', 'user.manage', 'role.manage', 'activity-log.view'])
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-[2px] mb-3" :class="sidebarCollapsed ? 'hidden' : 'block'">Sistem</p>
                    
                    @can('laporan.view')
                    <x-sidebar-dropdown title="Laporan" icon="fas fa-file-invoice-dollar" :active="request()->is('laporan*')">
                        <x-sidebar-submenu-item title="Laporan Barang" href="/laporan" :active="request()->is('laporan')" />
                        <x-sidebar-submenu-item title="Laporan Barang Masuk" href="/barang-masuk" :active="request()->is('barang-masuk')" />
                        <x-sidebar-submenu-item title="Laporan Barang Keluar" href="/barang-keluar" :active="request()->is('barang-keluar')" />
                        <x-sidebar-submenu-item title="Laporan Stock Opname" href="/stock-opname" :active="request()->is('stock-opname*')" />
                        @can('laporan.export')
                        <x-sidebar-submenu-item title="Export PDF / Excel" href="/laporan" :active="false" />
                        @endcan
                    </x-sidebar-dropdown>
                    @endcan

                    @canany(['user.manage', 'role.manage', 'activity-log.view'])
                    <x-sidebar-dropdown title="Manajemen User" icon="fas fa-user-shield" :active="request()->is('users*') || request()->is('jabatan*') || request()->is('bidang*') || request()->is('roles*') || request()->is('activity-log*')">
                        @can('user.manage')
                        <x-sidebar-submenu-item title="Data Karyawan" href="/users" :active="request()->is('users*')" />
                        <x-sidebar-submenu-item title="Referensi Jabatan" href="/jabatan" :active="request()->is('jabatan*')" />
                        <x-sidebar-submenu-item title="Referensi Bidang" href="/bidang" :active="request()->is('bidang*')" />
                        @endcan
                        @can('role.manage')
                        <x-sidebar-submenu-item title="Referensi Role" href="/roles" :active="request()->is('roles*')" />
                        <x-sidebar-submenu-item title="Hak Akses" href="/roles" :active="request()->is('roles*')" />
                        @endcan
                        @can('activity-log.view')
                        <x-sidebar-submenu-item title="Activity Log" href="/activity-log" :active="request()->is('activity-log*')" />
                        @endcan
                    </x-sidebar-dropdown>
                    @endcanany
                </div>
                @endcanany

            </div>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-800 shrink-0">
                <button @click="sidebarCollapsed = !sidebarCollapsed" 
                        class="flex items-center w-full px-4 py-2.5 text-sm font-medium text-gray-400 rounded-xl hover:bg-gray-800 hover:text-white transition-all duration-200">
                    <i :class="sidebarCollapsed ? 'fas fa-indent' : 'fas fa-outdent'" class="w-5 h-5 mr-3 flex items-center justify-center text-gray-500"></i>
                    <span :class="sidebarCollapsed ? 'hidden' : 'block'">Collapse Sidebar</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden">
            
            <!-- Navbar -->
            <header class="h-16 flex items-center justify-between px-8 bg-white dark:bg-navy-900 border-b border-gray-200 dark:border-gray-800 shrink-0 shadow-sm z-10">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="md:hidden text-gray-500 mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="text-gray-400">Pages /</span>
                        <span class="text-gray-900 dark:text-white font-semibold">@yield('title', 'Dashboard')</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-6">
                    <!-- Search -->
                    <div class="hidden lg:flex items-center bg-gray-100 dark:bg-gray-800 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700">
                        <i class="fas fa-search text-gray-400 text-xs"></i>
                        <input type="text" placeholder="Cari menu..." class="bg-transparent border-none focus:ring-0 text-xs text-gray-600 dark:text-gray-300 w-48 ml-2">
                    </div>

                    <!-- Dark Mode -->
                    <button @click="darkMode = !darkMode" class="text-gray-500 hover:text-primary-600 transition-colors">
                        <i :class="darkMode ? 'fas fa-sun' : 'fas fa-moon'" class="text-lg"></i>
                    </button>

                    <!-- Notifications -->
                    <div class="relative" x-data="{ 
                        open: false, 
                        notifications: [], 
                        get unreadCount() { return this.notifications.length },
                        fetchNotifications() {
                            fetch('/notifications')
                                .then(res => res.json())
                                .then(data => this.notifications = data);
                        },
                        markAsRead(id) {
                            fetch(`/notifications/${id}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Content-Type': 'application/json'
                                }
                            }).then(() => this.fetchNotifications());
                        },
                        typeColor(n) {
                            const t = n.data?.type || 'info';
                            if (t === 'danger') return 'bg-red-50 dark:bg-red-900/20 text-red-500';
                            if (t === 'warning') return 'bg-orange-50 dark:bg-orange-900/20 text-orange-500';
                            return 'bg-blue-50 dark:bg-blue-900/20 text-blue-500';
                        },
                        typeBorder(n) {
                            const t = n.data?.type || 'info';
                            if (t === 'danger') return 'border-l-red-500';
                            if (t === 'warning') return 'border-l-orange-400';
                            return 'border-l-blue-400';
                        }
                    }" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)">
                        <button @click="open = !open" class="relative text-gray-500 hover:text-primary-600 transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <template x-if="unreadCount > 0">
                                <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white dark:border-navy-900 px-1 font-bold animate-pulse" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                            </template>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="absolute right-0 mt-3 w-96 bg-white dark:bg-navy-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 z-50 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-navy-800/50 flex justify-between items-center border-b border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Notifikasi</h4>
                                    <template x-if="unreadCount > 0">
                                        <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" x-text="unreadCount"></span>
                                    </template>
                                </div>
                                <a href="/notifications/read-all" class="text-[10px] font-bold text-primary-600 hover:underline">Tandai Semua Baca</a>
                            </div>
                            <div class="max-h-[420px] overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-10 text-center">
                                        <i class="fas fa-bell-slash text-gray-300 dark:text-gray-700 text-3xl mb-3"></i>
                                        <p class="text-xs text-gray-400 font-medium">Tidak ada notifikasi baru</p>
                                    </div>
                                </template>
                                <template x-for="n in notifications.slice(0, 10)" :key="n.id">
                                    <div class="px-3 py-3 hover:bg-gray-50 dark:hover:bg-navy-800 border-b border-gray-50 dark:border-gray-800 last:border-0 transition-colors cursor-pointer border-l-4" 
                                         :class="typeBorder(n)"
                                         @click="markAsRead(n.id); window.location.href = n.data.item_id ? '/barang/' + n.data.item_id : (n.data.url || '/barang')">
                                        <div class="flex items-start gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5" :class="typeColor(n)">
                                                <i :class="n.data.icon || 'fas fa-info-circle'" class="text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold text-gray-900 dark:text-white leading-tight" x-text="n.data.title"></p>
                                                
                                                <!-- Stock alert detail -->
                                                <template x-if="n.data.alert_kind === 'stock'">
                                                    <div class="mt-1.5 space-y-0.5">
                                                        <p class="text-[11px] text-gray-600 dark:text-gray-300"><span class="font-semibold">Barang:</span> <span x-text="n.data.nama_barang"></span></p>
                                                        <p class="text-[10px] text-gray-500"><span class="font-semibold">Kode:</span> <span x-text="n.data.kode_barang" class="font-mono"></span></p>
                                                        <p class="text-[10px] text-gray-500"><span class="font-semibold">Lokasi:</span> <span x-text="n.data.lokasi"></span></p>
                                                        <div class="flex items-center gap-3 mt-1">
                                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md" :class="n.data.type === 'danger' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'">
                                                                Sisa: <span x-text="n.data.stok_saat_ini"></span> <span x-text="n.data.satuan"></span>
                                                            </span>
                                                            <span class="text-[10px] text-gray-400">Min: <span x-text="n.data.stok_minimal"></span></span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Expiry alert detail -->
                                                <template x-if="n.data.alert_kind === 'expiry'">
                                                    <div class="mt-1.5 space-y-0.5">
                                                        <p class="text-[11px] text-gray-600 dark:text-gray-300"><span class="font-semibold">Barang:</span> <span x-text="n.data.nama_barang"></span></p>
                                                        <p class="text-[10px] text-gray-500"><span class="font-semibold">Kode:</span> <span x-text="n.data.kode_barang" class="font-mono"></span></p>
                                                        <p class="text-[10px] text-gray-500"><span class="font-semibold">Lokasi:</span> <span x-text="n.data.lokasi"></span></p>
                                                        <div class="mt-1">
                                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md" :class="n.data.type === 'danger' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'" x-text="n.data.status_text"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Generic fallback -->
                                                <template x-if="!n.data.alert_kind">
                                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-snug" x-text="n.data.message"></p>
                                                </template>

                                                <p class="text-[9px] text-gray-400 mt-1.5" x-text="new Date(n.created_at).toLocaleString('id-ID', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <template x-if="notifications.length > 0">
                                <div class="px-4 py-3 bg-gray-50 dark:bg-navy-800/50 border-t border-gray-100 dark:border-gray-800 text-center">
                                    <a href="/notifications/read-all" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors">
                                        <i class="fas fa-list-check mr-1"></i> Lihat Semua Notifikasi
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="h-8 w-[1px] bg-gray-200 dark:bg-gray-700"></div>

                    <!-- Profile -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center focus:outline-none group">
                            <div class="flex flex-col items-end mr-3 hidden sm:flex">
                                <span class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">{{ Auth::user()->nama_lengkap }}</span>
                                <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>
                            </div>
                            <img src="{{ Auth::user()->foto ?? 'https://ui-avatars.com/api/?name='.Auth::user()->nama_lengkap.'&background=0284c7&color=fff' }}" class="w-10 h-10 rounded-xl border-2 border-gray-100 dark:border-gray-800 shadow-sm">
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="absolute right-0 mt-3 w-56 bg-white dark:bg-navy-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 py-2 z-50 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-navy-800/50 mb-2">
                                <p class="text-xs text-gray-500 mb-1">NIP</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ Auth::user()->nip }}</p>
                            </div>
                            <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-800 hover:text-primary-600 transition-all">
                                <i class="fas fa-user-circle mr-3 text-gray-400"></i> Profil Saya
                            </a>
                            @can('settings.manage')
                            <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-800 hover:text-primary-600 transition-all">
                                <i class="fas fa-cog mr-3 text-gray-400"></i> Pengaturan
                            </a>
                            @endcan
                            <hr class="my-2 border-gray-100 dark:border-gray-800">
                            <form action="/logout" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                                    <i class="fas fa-sign-out-alt mr-3"></i> Keluar Sistem
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50 dark:bg-navy-950">
                <div class="max-w-[1600px] mx-auto">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="h-12 px-8 flex items-center justify-between bg-white dark:bg-navy-900 border-t border-gray-200 dark:border-gray-800 text-[10px] text-gray-500 font-medium shrink-0">
                <p>{{ \App\Models\Setting::get('footer_text', '© 2026 Sistem Manajemen Logistik & Inventory Modern (SIDARLOG). All rights reserved.') }}</p>
                <div class="flex items-center space-x-4">
                    <span class="flex items-center"><span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> System Online</span>
                    <span>v2.0.4-enterprise</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" 
         @click="sidebarOpen = false"></div>

    <!-- Global Delete Confirmation Modal -->
    <div x-show="confirmDelete.open" 
         x-cloak 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-navy-950/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="confirmDelete.open = false" 
             class="bg-white dark:bg-navy-900 w-full max-w-sm rounded-[32px] overflow-hidden shadow-2xl border border-gray-100 dark:border-gray-800"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-red-50 dark:bg-red-900/20 rounded-3xl flex items-center justify-center text-red-600 mx-auto mb-6 shadow-inner">
                    <i class="fas fa-trash-can text-3xl"></i>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2" x-text="confirmDelete.title"></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-10 leading-relaxed" x-text="confirmDelete.message"></p>
                
                <div class="flex gap-3">
                    <button @click="confirmDelete.open = false" 
                            class="flex-1 py-4 bg-gray-50 dark:bg-navy-800 text-gray-600 dark:text-gray-300 font-bold rounded-2xl transition-all hover:bg-gray-100 dark:hover:bg-navy-700 active:scale-95">
                        Batal
                    </button>
                    <button @click="executeDelete()" 
                            class="flex-1 py-4 bg-red-600 text-white font-bold rounded-2xl shadow-xl shadow-red-500/30 transition-all hover:bg-red-700 active:scale-95 transform">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
