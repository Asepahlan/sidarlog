@props(['title', 'href' => '#', 'active' => false])

<a href="{{ $href }}" 
   class="block py-2 px-4 text-xs font-medium transition-all duration-200 rounded-lg 
   {{ $active 
      ? 'text-primary-400 bg-primary-400/10' 
      : 'text-gray-500 hover:text-white hover:bg-gray-800' }}">
    <div class="flex items-center">
        <span class="w-1.5 h-1.5 rounded-full mr-3 {{ $active ? 'bg-primary-400' : 'bg-gray-700 group-hover:bg-gray-500' }}"></span>
        {{ $title }}
    </div>
</a>
