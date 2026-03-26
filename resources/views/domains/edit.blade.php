@extends('layouts.app')

@section('title', 'Edit – ' . $domain->name)

@section('content')

<div class="max-w-xl">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('domains.index') }}" class="hover:text-indigo-600 transition">Domains</a>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
        <a href="{{ route('domains.show', $domain) }}" class="hover:text-indigo-600 transition">{{ $domain->name }}</a>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
        <span class="text-gray-900 font-medium">Edit</span>
    </nav>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
        <h1 class="text-xl font-bold text-gray-900 mb-1">Edit domain</h1>
        <p class="text-sm text-gray-500 mb-6">{{ $domain->name }}</p>

        @include('layouts._form', [
            'action' => route('domains.update', $domain),
            'method' => 'PUT',
        ])
    </div>

    {{-- Danger zone --}}
    <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4">
        <h2 class="text-sm font-semibold text-red-800 mb-2">Danger zone</h2>
        <p class="text-xs text-red-600 mb-3">
            Deleting the domain removes all check history permanently.
        </p>
        <form method="POST" action="{{ route('domains.destroy', $domain) }}"
              onsubmit="return confirm('Delete {{ addslashes($domain->name) }}? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-400 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
                Delete domain
            </button>
        </form>
    </div>
</div>

@endsection
