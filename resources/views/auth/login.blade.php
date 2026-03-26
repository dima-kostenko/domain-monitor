<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in – Domain Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center">

<div class="w-full max-w-sm px-4">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <svg class="mx-auto w-10 h-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253"/>
        </svg>
        <h1 class="mt-3 text-2xl font-bold text-gray-900">Domain Monitor</h1>
        <p class="text-sm text-gray-500">Sign in to your account</p>
    </div>

    {{-- Card --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
        <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
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
                    autocomplete="current-password"
                    class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="h-4 w-4 rounded border-gray-300 accent-indigo-600 cursor-pointer"
                >
                <label for="remember" class="text-sm text-gray-600 cursor-pointer">Remember me</label>
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm
                       hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                Sign in
            </button>
        </form>
    </div>

    <p class="mt-4 text-center text-sm text-gray-500">
        No account?
        <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">Create one</a>
    </p>
</div>

</body>
</html>
