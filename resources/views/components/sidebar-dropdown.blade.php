@props(['icon' => '', 'title', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="mb-1">
    <button @click="sidebarCollapsed ? sidebarCollapsed = false : open = !open" 
            class="w-full group flex items-center justify-between px-4 py-2.5 text-sm font-medium transition-all duration-200 rounded-xl
            {{ $active ? 'text-[#F97316] dark:text-[#FB923C] bg-[#FFF7ED]/40 dark:bg-orange-950/10 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:bg-[#F8FAFC] dark:hover:bg-[#334155]/30 hover:text-slate-800 dark:hover:text-[#F8FAFC]' }}">
        <div class="flex items-center">
            @if($icon)
                <i class="{{ $icon }} w-5 h-5 mr-3 flex items-center justify-center {{ $active ? 'text-[#F97316] dark:text-[#FB923C]' : 'text-slate-400 dark:text-slate-500 group-hover:text-[#F97316] dark:group-hover:text-[#FB923C]' }}"></i>
            @endif
            <span x-show="!sidebarCollapsed" x-transition>{{ $title }}</span>
        </div>
        <i x-show="!sidebarCollapsed" class="fas fa-chevron-right text-[10px] transition-transform duration-200" 
           :class="open ? 'rotate-90' : ''"></i>
    </button>
    
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         class="mt-1 space-y-1 px-4"
         x-cloak>
        {{ $slot }}
    </div>
</div>
