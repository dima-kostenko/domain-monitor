{{--
    Shared domain form partial.
    Props: $domain (Domain model), $action (url), $method ('POST'|'PUT')
--}}
<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    {{-- Domain name --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
            Domain name <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm pointer-events-none">
                https://
            </span>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $domain->name) }}"
                placeholder="example.com"
                autocomplete="off"
                class="block w-full rounded-lg border pl-[4.5rem] pr-3 py-2.5 text-sm shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
            >
        </div>
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Check interval + Timeout (side by side) --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="check_interval" class="block text-sm font-medium text-gray-700 mb-1">
                Check interval <span class="text-red-500">*</span>
                <span class="font-normal text-gray-400">(min)</span>
            </label>
            <input
                type="number"
                id="check_interval"
                name="check_interval"
                value="{{ old('check_interval', $domain->check_interval ?? 5) }}"
                min="1"
                max="1440"
                class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       {{ $errors->has('check_interval') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
            >
            @error('check_interval')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="timeout" class="block text-sm font-medium text-gray-700 mb-1">
                Timeout <span class="text-red-500">*</span>
                <span class="font-normal text-gray-400">(sec)</span>
            </label>
            <input
                type="number"
                id="timeout"
                name="timeout"
                value="{{ old('timeout', $domain->timeout ?? 10) }}"
                min="1"
                max="60"
                class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       {{ $errors->has('timeout') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
            >
            @error('timeout')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- HTTP method --}}
    <div>
        <span class="block text-sm font-medium text-gray-700 mb-2">
            HTTP method <span class="text-red-500">*</span>
        </span>
        <div class="flex gap-4">
            @foreach(['HEAD', 'GET'] as $m)
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    type="radio"
                    name="method"
                    value="{{ $m }}"
                    {{ old('method', $domain->method ?? 'HEAD') === $m ? 'checked' : '' }}
                    class="accent-indigo-600"
                >
                <span class="text-sm text-gray-700">
                    {{ $m }}
                    @if($m === 'HEAD')
                        <span class="text-xs text-gray-400">(faster, no body)</span>
                    @else
                        <span class="text-xs text-gray-400">(full response)</span>
                    @endif
                </span>
            </label>
            @endforeach
        </div>
        @error('method')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Active toggle --}}
    <div class="flex items-center gap-3">
        <input
            type="hidden"
            name="is_active"
            value="0"
        >
        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            {{ old('is_active', $domain->is_active ?? true) ? 'checked' : '' }}
            class="h-4 w-4 rounded border-gray-300 accent-indigo-600 cursor-pointer"
        >
        <label for="is_active" class="text-sm text-gray-700 cursor-pointer">
            Enable monitoring
        </label>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 pt-2">
        <button
            type="submit"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            {{ $method === 'PUT' ? 'Save changes' : 'Add domain' }}
        </button>
        <a href="{{ route('domains.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 transition">
            Cancel
        </a>
    </div>
</form>
