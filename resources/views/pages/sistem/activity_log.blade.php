@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Log Aktivitas Sistem</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Rekaman seluruh aktivitas operasional pengguna dalam sistem.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="px-4 py-2 bg-white dark:bg-navy-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-50">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            <button class="px-4 py-2 bg-navy-900 dark:bg-navy-800 text-white rounded-xl text-xs font-bold border border-navy-800">
                <i class="fas fa-download mr-2"></i> Export Log
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-navy-800/50">
                    <tr>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pengguna</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Modul</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aktivitas</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-800/30 transition-all duration-200">
                        <td class="px-4 py-5">
                            <span class="text-xs font-bold text-gray-400">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                        </td>
                        <td class="px-4 py-5">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-navy-800 flex items-center justify-center mr-3 text-[10px] font-bold text-gray-500">
                                    {{ substr($log->pengguna->nama_lengkap ?? 'Sys', 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $log->pengguna->nama_lengkap ?? 'System' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $log->pengguna->nip ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-5">
                            <span class="px-2 py-1 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 text-[10px] font-bold rounded uppercase tracking-tighter">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td class="px-4 py-5">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $log->activity }}</p>
                        </td>
                        <td class="px-4 py-5">
                            <span class="text-xs font-mono text-gray-400">{{ $log->ip_address }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400 italic text-sm">Tidak ada rekaman aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 border-t border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-navy-800/20">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
