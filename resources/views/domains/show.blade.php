@extends('layouts.app')

@section('title', $domain->name)

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Domains', 'url' => route('domains.index')],
    ['label' => $domain->name],
]"/>

{{-- ─── Header ──────────────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            @php $latest = $domain->latestCheck; @endphp
            <x-status-badge :status="$domain->latestCheck?->status ?? ($domain->is_active ? 'pending' : 'paused')"/>
            <h1 class="text-2xl font-bold text-gray-900">{{ $domain->name }}</h1>
        </div>
        <p class="text-sm text-gray-500 mt-1">
            Check every {{ $domain->check_interval }} min &middot;
            Timeout {{ $domain->timeout }}s &middot;
            <span class="font-mono bg-gray-100 rounded px-1 text-xs">{{ $domain->method }}</span>
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('domains.edit', $domain) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            </svg>
            Edit
        </a>
    </div>
</div>

{{-- ─── Stats cards ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <x-stat-card label="Total checks"
                 :value="number_format($stats['total'])"/>
    <x-stat-card label="Uptime"
                 :value="$stats['uptime'] !== null ? $stats['uptime'].'%' : '–'"
                 :sub="$stats['total'] > 0 ? $stats['online'].' online / '.$stats['total'].' total' : null"
                 color="green"/>
    <x-stat-card label="Avg response"
                 :value="$stats['avg_time'] !== null ? round($stats['avg_time']).' ms' : '–'"
                 color="indigo"/>
    <x-stat-card label="Last checked"
                 :value="$latest?->created_at->diffForHumans() ?? 'Never'"
                 :sub="$latest?->created_at->format('d M Y H:i')"
                 color="gray"/>
</div>

{{-- ─── Recent checks preview ──────────────────────────────────────────────── --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">Recent checks</h2>
        <a href="{{ route('domain-checks.index', $domain) }}"
           class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:underline font-medium">
            View full history
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
    </div>

    @if($checks->isEmpty())
        <p class="text-center text-sm text-gray-400 py-10">No checks yet.</p>
    @else
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-xs">
                <tr>
                    <th class="px-4 py-2.5 text-left font-semibold text-gray-500">Time</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-gray-500">Status</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-gray-500 hidden sm:table-cell">HTTP code</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-gray-500 hidden sm:table-cell">Response time</th>
                    <th class="px-4 py-2.5 text-left font-semibold text-gray-500">Error</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($checks as $check)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-2.5 whitespace-nowrap">
                        <span class="text-gray-800">{{ $check->created_at->format('d M') }}</span>
                        <span class="text-gray-400 text-xs ml-1">{{ $check->created_at->format('H:i:s') }}</span>
                    </td>
                    <td class="px-4 py-2.5">
                        <x-status-badge :status="$check->status"/>
                    </td>
                    <td class="px-4 py-2.5 hidden sm:table-cell">
                        @if($check->response_code)
                            <span class="font-mono text-xs text-gray-600">{{ $check->response_code }}</span>
                        @else
                            <span class="text-gray-300">–</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 hidden sm:table-cell text-gray-600">
                        {{ $check->response_time_formatted }}
                    </td>
                    <td class="px-4 py-2.5 text-red-600 text-xs max-w-xs truncate"
                        title="{{ $check->error_message }}">
                        {{ $check->error_message ?? '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-400">Showing last {{ $checks->count() }} checks</span>
            <a href="{{ route('domain-checks.index', $domain) }}"
               class="text-xs text-indigo-600 hover:underline">
                View all {{ number_format($stats['total']) }} checks →
            </a>
        </div>
    @endif
</div>

@endsection
