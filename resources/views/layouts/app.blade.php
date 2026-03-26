<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Domain Monitor') – CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex flex-col font-sans text-gray-800">

{{-- ─── Navbar ─────────────────────────────────────────────────────────────── --}}
<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">

            {{-- Left: Logo + Nav links --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 font-bold text-indigo-600 text-lg tracking-tight shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253"/>
                    </svg>
                    Domain Monitor
                </a>

                <div class="hidden sm:flex items-center gap-1">
                    @php
                        $navLinks = [
                            ['route' => 'dashboard',     'label' => 'Dashboard'],
                            ['route' => 'domains.index', 'label' => 'Domains'],
                        ];
                    @endphp
                    @foreach($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="px-3 py-1.5 rounded-md text-sm transition
                                  {{ request()->routeIs($link['route']) || request()->routeIs($link['route'] . '.*')
                                     ? 'bg-indigo-50 text-indigo-700 font-medium'
                                     : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Right: User dropdown --}}
            <div class="relative" id="user-menu-wrapper">
                <button id="user-menu-btn"
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-100 transition text-sm"
                        aria-expanded="false">
                    {{-- Avatar initials --}}
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-indigo-600 text-white text-xs font-bold select-none">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden sm:block text-gray-700 max-w-[140px] truncate">{{ auth()->user()->name }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>

                {{-- Dropdown panel --}}
                <div id="user-menu"
                     class="hidden absolute right-0 mt-1 w-52 rounded-xl border border-gray-200 bg-white shadow-lg z-50 py-1 text-sm">
                    <div class="px-3 py-2 border-b border-gray-100">
                        <p class="font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->is_admin)
                            <span class="inline-block mt-1 rounded-full bg-indigo-100 px-2 py-0.5 text-xs text-indigo-700 font-medium">Admin</span>
                        @endif
                    </div>
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                        Profile settings
                    </a>
                    <div class="border-t border-gray-100 mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex w-full items-center gap-2 px-3 py-2 text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                                </svg>
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- ─── Flash messages ─────────────────────────────────────────────────────── --}}
@foreach(['success' => 'green', 'error' => 'red', 'warning' => 'yellow'] as $type => $color)
@if(session($type))
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4 flash-message">
    <div class="flex items-start gap-3 rounded-lg bg-{{ $color }}-50 border border-{{ $color }}-200 px-4 py-3 text-sm text-{{ $color }}-800">
        <svg class="w-4 h-4 mt-0.5 shrink-0 text-{{ $color }}-500" fill="currentColor" viewBox="0 0 20 20">
            @if($type === 'success')
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            @else
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
            @endif
        </svg>
        <span>{{ session($type) }}</span>
        <button onclick="this.closest('.flash-message').remove()"
                class="ml-auto text-{{ $color }}-400 hover:text-{{ $color }}-700">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif
@endforeach

{{-- ─── Main content ────────────────────────────────────────────────────────── --}}
<main class="flex-1 mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>

<footer class="border-t border-gray-200 text-center text-xs text-gray-400 py-4">
    Domain Monitor &copy; {{ date('Y') }}
</footer>

<script>
    // User dropdown toggle
    const btn  = document.getElementById('user-menu-btn');
    const menu = document.getElementById('user-menu');
    btn.addEventListener('click', () => {
        const open = menu.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', String(!open));
    });
    document.addEventListener('click', (e) => {
        if (!document.getElementById('user-menu-wrapper').contains(e.target)) {
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    // Auto-dismiss flash messages after 5 s
    document.querySelectorAll('.flash-message').forEach(el => {
        setTimeout(() => el.remove(), 5000);
    });
</script>
</body>
</html>
