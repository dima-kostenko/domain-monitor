@extends('layouts.app')

@section('title', 'Check history – ' . $domain->name)

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Domains',      'url' => route('domains.index')],
    ['label' => $domain->name,  'url' => route('domains.show', $domain)],
    ['label' => 'Check history'],
]"/>

{{-- ─── Header ──────────────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Check history</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ $domain->name }}
            @if($domain->latestCheck)
                &middot; last checked {{ $domain->latestCheck->created_at->diffForHumans() }}
            @endif
        </p>
    </div>
    <x-status-badge :status="$domain->latestCheck?->status ?? ($domain->is_active ? 'pending' : 'paused')"/>
</div>

{{-- ─── Stats ───────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
    <x-stat-card label="Total checks" :value="number_format($stats['total'])"/>
    <x-stat-card label="Online"        :value="$stats['online']"   color="green"/>
    <x-stat-card label="Offline"       :value="$stats['offline']"  color="red"/>
    <x-stat-card label="Uptime"
                 :value="$stats['uptime'] !== null ? $stats['uptime'].'%' : '–'"
                 color="indigo"/>
    <x-stat-card label="Avg response"
                 :value="$stats['avg_time'] !== null ? $stats['avg_time'].' ms' : '–'"
                 color="gray"/>
</div>

{{-- ─── Filters ─────────────────────────────────────────────────────────────── --}}
<form method="GET"
      action="{{ route('domain-checks.index', $domain) }}"
      class="rounded-xl border border-gray-200 bg-white shadow-sm p-4 mb-4">

    <div class="flex flex-wrap items-end gap-3">

        {{-- Status --}}
        <div class="flex flex-col gap-1 min-w-[120px]">
            <label class="text-xs font-medium text-gray-600">Status</label>
            <select name="status"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All</option>
                <option value="online"  {{ ($filters['status'] ?? '') === 'online'  ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ ($filters['status'] ?? '') === 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
        </div>

        {{-- Date from --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-600">From</label>
            <input type="date"
                   name="date_from"
                   value="{{ $filters['date_from'] ?? '' }}"
                   max="{{ now()->toDateString() }}"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('date_from')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Date to --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-600">To</label>
            <input type="date"
                   name="date_to"
                   value="{{ $filters['date_to'] ?? '' }}"
                   max="{{ now()->toDateString() }}"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('date_to')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Per page --}}
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-600">Per page</label>
            <select name="per_page"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach([25, 50, 100] as $n)
                    <option value="{{ $n }}" {{ ((int)($filters['per_page'] ?? 25)) === $n ? 'selected' : '' }}>
                        {{ $n }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 pb-0.5">
            <button type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                Apply
            </button>
            @if(array_filter($filters))
                <a href="{{ route('domain-checks.index', $domain) }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
                    Reset
                </a>
            @endif
        </div>

    </div>

    {{-- Active filter pills --}}
    @if(array_filter($filters))
    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
        @if(!empty($filters['status']))
            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs text-indigo-700">
                Status: {{ ucfirst($filters['status']) }}
            </span>
        @endif
        @if(!empty($filters['date_from']))
            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs text-indigo-700">
                From: {{ $filters['date_from'] }}
            </span>
        @endif
        @if(!empty($filters['date_to']))
            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs text-indigo-700">
                To: {{ $filters['date_to'] }}
            </span>
        @endif
    </div>
    @endif

</form>

{{-- ─── Table ───────────────────────────────────────────────────────────────── --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">

    {{-- Table toolbar --}}
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
        <span class="text-sm text-gray-500">
            @if($checks->total() > 0)
                Showing {{ $checks->firstItem() }}–{{ $checks->lastItem() }}
                of {{ number_format($checks->total()) }} checks
            @else
                No results
            @endif
        </span>
        <span class="text-xs text-gray-400">Latest first</span>
    </div>

    @if($checks->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <svg class="mx-auto w-10 h-10 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">No checks match the current filters</p>
            @if(array_filter($filters))
                <a href="{{ route('domain-checks.index', $domain) }}"
                   class="text-xs text-indigo-600 hover:underline mt-1 inline-block">
                    Clear filters
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500 whitespace-nowrap">Date &amp; time</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">HTTP code</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Response time</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Error message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($checks as $check)
                    <tr class="hover:bg-gray-50 transition group">

                        {{-- Date --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-gray-800 font-medium">{{ $check->created_at->format('d M Y') }}</span>
                            <span class="text-gray-400 text-xs ml-1.5">{{ $check->created_at->format('H:i:s') }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            <x-status-badge :status="$check->status"/>
                        </td>

                        {{-- HTTP code --}}
                        <td class="px-4 py-3">
                            @if($check->response_code)
                                @php
                                    $codeColor = match(true) {
                                        $check->response_code < 300 => 'text-green-700 bg-green-50',
                                        $check->response_code < 400 => 'text-blue-700 bg-blue-50',
                                        $check->response_code < 500 => 'text-yellow-700 bg-yellow-50',
                                        default                      => 'text-red-700 bg-red-50',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-mono font-semibold {{ $codeColor }}">
                                    {{ $check->response_code }}
                                </span>
                            @else
                                <span class="text-gray-300">–</span>
                            @endif
                        </td>

                        {{-- Response time --}}
                        <td class="px-4 py-3">
                            @if($check->response_time !== null)
                                @php
                                    $timeColor = match(true) {
                                        $check->response_time < 300  => 'text-green-600',
                                        $check->response_time < 1000 => 'text-yellow-600',
                                        default                       => 'text-red-600',
                                    };
                                @endphp
                                <span class="font-mono text-sm {{ $timeColor }}">
                                    {{ $check->response_time_formatted }}
                                </span>
                            @else
                                <span class="text-gray-300">–</span>
                            @endif
                        </td>

                        {{-- Error --}}
                        <td class="px-4 py-3 max-w-xs">
                            @if($check->error_message)
                                <span class="text-xs text-red-600 break-words"
                                      title="{{ $check->error_message }}">
                                    {{ Str::limit($check->error_message, 80) }}
                                </span>
                            @else
                                <span class="text-gray-300">–</span>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($checks->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between gap-4">
                <span class="text-xs text-gray-400">
                    Page {{ $checks->currentPage() }} of {{ $checks->lastPage() }}
                </span>
                {{ $checks->links() }}
            </div>
        @endif
    @endif

</div>

@endsection
