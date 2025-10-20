@php
    $palette = [
        'green' => 'bg-green-100 text-green-800',
        'red' => 'bg-red-100 text-red-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'gray' => 'bg-gray-100 text-gray-800',
    ];

    $classes = $palette[$color] ?? $palette['gray'];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full ' . $classes,
]) }}>
    {{ $slot }}
</span>
