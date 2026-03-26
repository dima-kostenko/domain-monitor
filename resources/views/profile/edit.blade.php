@extends('layouts.app')

@section('title', 'Profile settings')

@section('content')

<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Profile settings</h1>

    <div class="space-y-6">

        {{-- ─── Profile info ───────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Account information</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" novalidate>
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Save changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── Change password ─────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Change password</h2>
            <p class="text-sm text-gray-500 mb-4">Use a strong password with letters and numbers.</p>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4" novalidate>
                @csrf
                @method('PATCH')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Current password
                    </label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        required
                        autocomplete="current-password"
                        class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               {{ $errors->has('current_password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    >
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                        New password
                    </label>
                    <input
                        type="password"
                        id="new_password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm new password
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

                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Update password
                </button>
            </form>
        </div>

        {{-- ─── Account info --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-3">Account details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex gap-3">
                    <dt class="w-32 text-gray-500 shrink-0">Member since</dt>
                    <dd class="text-gray-900">{{ $user->created_at->format('d M Y') }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-32 text-gray-500 shrink-0">Domains</dt>
                    <dd class="text-gray-900">{{ $user->domains()->count() }}</dd>
                </div>
                @if($user->is_admin)
                <div class="flex gap-3">
                    <dt class="w-32 text-gray-500 shrink-0">Role</dt>
                    <dd>
                        <span class="inline-flex rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700">
                            Administrator
                        </span>
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- ─── Danger zone ──────────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-red-200 bg-red-50 p-6">
            <h2 class="text-base font-semibold text-red-800 mb-1">Delete account</h2>
            <p class="text-sm text-red-600 mb-4">
                Permanently deletes your account and all associated domains and check history. This cannot be undone.
            </p>

            <button id="delete-account-btn"
                    class="rounded-lg border border-red-400 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100 transition">
                Delete my account
            </button>

            {{-- Hidden confirm form --}}
            <div id="delete-account-form" class="hidden mt-4 space-y-3">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label class="block text-sm font-medium text-red-800 mb-1">
                            Confirm your password to delete
                        </label>
                        <input
                            type="password"
                            name="password"
                            required
                            placeholder="Your current password"
                            class="block w-full rounded-lg border border-red-300 px-3 py-2.5 text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-red-400
                                   {{ $errors->has('password') ? 'border-red-500' : '' }}"
                        >
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                            Yes, delete my account
                        </button>
                        <button type="button" id="cancel-delete-btn"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-white transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    const showBtn   = document.getElementById('delete-account-btn');
    const cancelBtn = document.getElementById('cancel-delete-btn');
    const form      = document.getElementById('delete-account-form');

    showBtn.addEventListener('click',   () => { form.classList.remove('hidden'); showBtn.classList.add('hidden'); });
    cancelBtn.addEventListener('click', () => { form.classList.add('hidden');    showBtn.classList.remove('hidden'); });
</script>

@if($errors->has('password') && old('_method') === 'DELETE')
<script>
    // Re-open delete form if it had errors
    document.getElementById('delete-account-form').classList.remove('hidden');
    document.getElementById('delete-account-btn').classList.add('hidden');
</script>
@endif

@endsection
