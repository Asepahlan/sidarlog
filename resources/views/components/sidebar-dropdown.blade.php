@props(['icon' => '', 'title', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="mb-1">
    <button @click="sidebarCollapsed ? sidebarCollapsed = false : open = !open" 
            class="w-full group flex items-center justify-between px-4 py-2.5 text-sm font-medium transition-all duration-200 rounded-xl
            {{ $active ? 'text-white bg-gray-800/50' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        <div class="flex items-center">
            @if($icon)
                <i class="{{ $icon }} w-5 h-5 mr-3 flex items-center justify-center {{ $active ? 'text-primary-400' : 'text-gray-500 group-hover:text-primary-400' }}"></i>
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
