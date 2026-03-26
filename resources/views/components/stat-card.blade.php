@props([
    'label',
    'value',
    'sub'   => null,
    'color' => 'gray',   // gray | green | red | yellow | indigo
])

@php
    $colors = [
        'gray'   => ['bg' => 'bg-gray-50',   'text' => 'text-gray-600'],
        'green'  => ['bg' => 'bg-green-50',  'text' => 'text-green-600'],
        'red'    => ['bg' => 'bg-red-50',    'text' => 'text-red-600'],
        'yellow' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-600'],
        'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
    ];
    $c = $colors[$color] ?? $colors['gray'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white p-4 shadow-sm']) }}>
    <p class="text-xs text-gray-500 mb-1">{{ $label }}</p>
    <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
    @if($sub)
        <p class="text-xs mt-1 {{ $c['text'] }}">{{ $sub }}</p>
    @endif
</div>
