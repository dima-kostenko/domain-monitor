@extends('layouts.app')

@section('title', 'My Domains')

@section('content')

{{-- ─── Header ──────────────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Domains</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $domains->total() }} domain(s) monitored</p>
    </div>
    <a href="{{ route('domains.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add domain
    </a>
</div>

{{-- ─── Table ───────────────────────────────────────────────────────────────── --}}
@if($domains->isEmpty())
    <div class="text-center py-20 text-gray-400">
        <svg class="mx-auto w-12 h-12 mb-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253"/>
        </svg>
        <p class="text-base font-medium text-gray-500">No domains yet</p>
        <p class="text-sm mt-1">
            <a href="{{ route('domains.create') }}" class="text-indigo-600 hover:underline">Add your first domain</a>
            to start monitoring.
        </p>
    </div>
@else
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Domain</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden sm:table-cell">Response</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Interval</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Method</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden lg:table-cell">Checks</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($domains as $domain)
                @php
                    $latest = $domain->latestCheck;
                    $isOnline = $latest?->status === 'online';
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            @if(!$domain->is_active)
                                <span class="inline-block w-2 h-2 rounded-full bg-gray-300"></span>
                            @elseif($latest === null)
                                <span class="inline-block w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                            @elseif($isOnline)
                                <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span>
                            @else
                                <span class="inline-block w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            @endif
                            <a href="{{ route('domains.show', $domain) }}"
                               class="font-medium text-gray-900 hover:text-indigo-600 transition">
                                {{ $domain->name }}
                            </a>
                        </div>
                        @if(!$domain->is_active)
                            <span class="text-xs text-gray-400 ml-4">paused</span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        @if(!$domain->is_active)
                            <span class="text-xs text-gray-400">–</span>
                        @elseif($latest === null)
                            <span class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-0.5 text-xs font-medium text-yellow-700 ring-1 ring-yellow-200">
                                Pending
                            </span>
                        @elseif($isOnline)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-green-200">
                                Online
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 ring-1 ring-red-200">
                                Offline
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3 hidden sm:table-cell text-gray-600">
                        @if($latest && $isOnline)
                            <span class="font-mono">{{ $latest->response_time_formatted }}</span>
                            <span class="text-gray-400 text-xs ml-1">HTTP {{ $latest->response_code }}</span>
                        @else
                            <span class="text-gray-400">–</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 hidden md:table-cell text-gray-500">
                        every {{ $domain->check_interval }} min
                    </td>

                    <td class="px-4 py-3 hidden md:table-cell">
                        <span class="inline-flex items-center rounded bg-gray-100 px-1.5 py-0.5 text-xs font-mono text-gray-600">
                            {{ $domain->method }}
                        </span>
                    </td>

                    <td class="px-4 py-3 hidden lg:table-cell text-gray-500">
                        {{ number_format($domain->checks_count) }}
                    </td>

                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('domains.edit', $domain) }}"
                           class="text-gray-400 hover:text-indigo-600 transition mr-3" title="Edit">
                            <svg class="inline w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('domains.destroy', $domain) }}"
                              class="inline"
                              onsubmit="return confirm('Delete {{ addslashes($domain->name) }}? All check history will be lost.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-gray-400 hover:text-red-600 transition" title="Delete">
                                <svg class="inline w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($domains->hasPages())
        <div class="mt-4">
            {{ $domains->links() }}
        </div>
    @endif
@endif

@endsection
