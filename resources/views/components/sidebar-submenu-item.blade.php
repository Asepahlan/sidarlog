@props(['title', 'href' => '#', 'active' => false])

<a href="{{ $href }}" 
   class="block py-2 px-4 text-xs font-medium transition-all duration-200 rounded-lg 
   {{ $active 
      ? 'text-[#F97316] dark:text-[#FB923C] bg-[#FFF7ED] dark:bg-orange-950/20 font-semibold' 
      : 'text-slate-500 dark:text-slate-400 hover:bg-[#F8FAFC] dark:hover:bg-[#334155]/30 hover:text-slate-800 dark:hover:text-[#F8FAFC]' }}">
    <div class="flex items-center">
        <span class="w-1.5 h-1.5 rounded-full mr-3 {{ $active ? 'bg-[#F97316] dark:bg-[#FB923C]' : 'bg-slate-300 dark:bg-slate-600 group-hover:bg-slate-400 dark:group-hover:bg-slate-500' }}"></span>
        {{ $title }}
    </div>
</a>
