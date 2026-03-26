@extends('layouts.app')

@section('title', 'Add Domain')

@section('content')

<div class="max-w-xl">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('domains.index') }}" class="hover:text-indigo-600 transition">Domains</a>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
        <span class="text-gray-900 font-medium">Add domain</span>
    </nav>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
        <h1 class="text-xl font-bold text-gray-900 mb-6">Add domain to monitor</h1>

        @include('layouts._form', [
            'action' => route('domains.store'),
            'method' => 'POST',
        ])
    </div>
</div>

@endsection
