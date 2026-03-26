<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account – Domain Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center py-12">

<div class="w-full max-w-sm px-4">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <svg class="mx-auto w-10 h-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253"/>
        </svg>
        <h1 class="mt-3 text-2xl font-bold text-gray-900">Create account</h1>
        <p class="text-sm text-gray-500">Start monitoring your domains for free</p>
    </div>

    {{-- Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
        <form method="POST" action="{{ route('register') }}" class="space-y-4" novalidate>
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="John Doe"
                    class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    placeholder="you@example.com"
                    class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >
                <p class="mt-1 text-xs text-gray-400">At least 8 characters with letters and numbers.</p>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm password
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm
                       hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                Create account
            </button>
        </form>
    </div>

    <p class="mt-4 text-center text-sm text-gray-500">
        Already have an account?
        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">Sign in</a>
    </p>
</div>

</body>
</html>
