@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ─── Header ──────────────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Hello, {{ auth()->user()->name }}
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Here's your monitoring overview</p>
    </div>
    <a href="{{ route('domains.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add domain
    </a>
</div>

{{-- ─── Stats ───────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            [
                'label'   => 'Total domains',
                'value'   => $stats['total'],
                'color'   => 'indigo',
                'icon'    => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3',
            ],
            [
                'label'   => 'Online',
                'value'   => $stats['online'],
                'color'   => 'green',
                'icon'    => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'label'   => 'Offline',
                'value'   => $stats['offline'],
                'color'   => 'red',
                'icon'    => 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'label'   => 'Pending',
                'value'   => $stats['pending'],
                'color'   => 'yellow',
                'icon'    => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
        ];
    @endphp

    @foreach($cards as $card)
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-center gap-4">
        <div class="flex-shrink-0 rounded-lg bg-{{ $card['color'] }}-50 p-2.5">
            <svg class="w-5 h-5 text-{{ $card['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-6">

    {{-- ─── Domain status list ──────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Domains</h2>
            <a href="{{ route('domains.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>

        @if($domains->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <p class="text-sm">No domains yet.</p>
                <a href="{{ route('domains.create') }}" class="text-sm text-indigo-600 hover:underline mt-1 inline-block">
                    Add your first domain →
                </a>
            </div>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($domains->take(8) as $domain)
                @php $latest = $domain->latestCheck; @endphp
                <li class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                    {{-- Status dot --}}
                    @if(!$domain->is_active)
                        <span class="w-2 h-2 rounded-full bg-gray-300 shrink-0"></span>
                    @elseif($latest === null)
                        <span class="w-2 h-2 rounded-full bg-yellow-400 shrink-0 animate-pulse"></span>
                    @elseif($latest->status === 'online')
                        <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-red-500 shrink-0 animate-pulse"></span>
                    @endif

                    <div class="flex-1 min-w-0">
                        <a href="{{ route('domains.show', $domain) }}"
                           class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition truncate block">
                            {{ $domain->name }}
                        </a>
                        <p class="text-xs text-gray-400">
                            @if($latest)
                                {{ $latest->created_at->diffForHumans() }}
                            @else
                                Not checked yet
                            @endif
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        @if($latest && $latest->status === 'online')
                            <span class="text-xs font-mono text-gray-600">{{ $latest->response_time_formatted }}</span>
                        @elseif($latest && $latest->status === 'offline')
                            <span class="text-xs text-red-500 truncate max-w-[100px] block">{{ Str::limit($latest->error_message, 20) }}</span>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>

            @if($domains->count() > 8)
                <div class="px-4 py-2 border-t border-gray-100 text-center">
                    <a href="{{ route('domains.index') }}" class="text-xs text-indigo-600 hover:underline">
                        +{{ $domains->count() - 8 }} more domains
                    </a>
                </div>
            @endif
        @endif
    </div>

    {{-- ─── Recent activity ──────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Recent activity</h2>
        </div>

        @if($recentChecks->isEmpty())
            <p class="text-center text-sm text-gray-400 py-10">No activity yet.</p>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($recentChecks as $check)
                <li class="px-4 py-2.5 flex items-start gap-3">
                    <div class="mt-0.5 shrink-0">
                        @if($check->status === 'online')
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 mt-1"></span>
                        @else
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-red-500 mt-1 animate-pulse"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-800 truncate">{{ $check->domain->name }}</p>
                        <p class="text-xs text-gray-400">
                            @if($check->status === 'online')
                                HTTP {{ $check->response_code }} &middot; {{ $check->response_time_formatted }}
                            @else
                                {{ Str::limit($check->error_message, 30) }}
                            @endif
                        </p>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap shrink-0">
                        {{ $check->created_at->diffForHumans(null, true) }}
                    </span>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>

@endsection
