@props(['active' => false, 'icon' => '', 'title', 'href' => '#'])

<a href="{{ $href }}" 
   class="group flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-200 rounded-xl mb-1
   {{ $active 
      ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' 
      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
    @if($icon)
        <i class="{{ $icon }} w-5 h-5 mr-3 flex items-center justify-center {{ $active ? 'text-white' : 'text-gray-500 group-hover:text-primary-400' }}"></i>
    @endif
    <span x-show="!sidebarCollapsed" x-transition>{{ $title }}</span>
</a>
