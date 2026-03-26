@props(['status'])

@php
    $map = [
        'online'  => 'bg-green-50 text-green-700 ring-green-200',
        'offline' => 'bg-red-50   text-red-700   ring-red-200',
        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        'paused'  => 'bg-gray-100  text-gray-600  ring-gray-200',
    ];
    $classes = $map[$status] ?? $map['pending'];

    $dots = [
        'online'  => 'bg-green-500',
        'offline' => 'bg-red-500 animate-pulse',
        'pending' => 'bg-yellow-400 animate-pulse',
    ];
    $dot = $dots[$status] ?? null;

    $labels = [
        'online'  => 'Online',
        'offline' => 'Offline',
        'pending' => 'Pending',
        'paused'  => 'Paused',
    ];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 $classes"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
    @endif
    {{ $labels[$status] ?? ucfirst($status) }}
</span>
