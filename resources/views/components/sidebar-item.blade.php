@props(['active' => false, 'icon' => '', 'title', 'href' => '#'])

<a href="{{ $href }}" 
   class="group flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 rounded-xl mb-1
   {{ $active 
      ? 'bg-[#FFF7ED] dark:bg-orange-950/20 text-[#F97316] dark:text-[#FB923C] font-semibold border-l-4 border-[#F97316] dark:border-[#FB923C] pl-[12px]' 
      : 'text-slate-500 dark:text-slate-400 hover:bg-[#F8FAFC] dark:hover:bg-[#334155]/30 hover:text-slate-800 dark:hover:text-[#F8FAFC]' }}">
    @if($icon)
        <i class="{{ $icon }} w-5 h-5 mr-3 flex items-center justify-center {{ $active ? 'text-[#F97316] dark:text-[#FB923C]' : 'text-slate-400 dark:text-slate-500 group-hover:text-[#F97316] dark:group-hover:text-[#FB923C]' }}"></i>
    @endif
    <span x-show="!sidebarCollapsed" x-transition>{{ $title }}</span>
</a>
