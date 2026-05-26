@props(['active' => false, 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-lg transition-all duration-200'
            : 'flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-primary-600 dark:hover:text-white rounded-lg transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="{{ $icon }} w-5 h-5 mr-3 flex items-center justify-center"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
