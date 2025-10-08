<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    <span class="block text-sm font-medium text-gray-500">{{ $label }}</span>
    <div class="text-gray-900">
        @if (! $slot->isEmpty())
            {{ $slot }}
        @elseif (! is_null($value))
            {{ $value }}
        @else
            -
        @endif
    </div>
</div>
