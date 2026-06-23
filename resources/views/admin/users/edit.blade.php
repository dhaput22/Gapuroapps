@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="space-y-4 text-[13px] text-gray-700">
    <div class="flex items-center justify-between border border-gray-200 bg-gray-100 px-4 py-2">
        <p class="font-semibold">
            Current Period : <span class="font-medium">2025-10-01 - 2025-12-31</span>
        </p>
        <p class="text-sm">
            <span class="font-semibold">System Admin</span>
            <span class="mx-2 text-gray-400">|</span>
            User Management
        </p>
    </div>

    <h1 class="text-center text-2xl font-semibold text-gray-700">Edit User</h1>

    @if ($errors->any())
    <div class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="rounded border border-gray-200 bg-gray-50 px-4 py-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" readonly
                    class="h-9 w-full rounded border border-gray-300 bg-gray-100 px-2 text-sm text-gray-500 cursor-not-allowed">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Role</label>
                <select name="role"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    @foreach ($availableRoles as $roleValue => $roleLabel)
                    <option value="{{ $roleValue }}" {{ old('role', $user->role) === $roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">New Password <span class="normal-case font-normal text-gray-400">(leave blank if you don't want to change)</span></label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="h-9 w-full rounded border border-gray-300 bg-white px-2 pr-9 text-sm focus:border-gray-400 focus:outline-none"
                        placeholder="••••••••" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password', 'eye-password')"
                        class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 hover:text-gray-600">
                        <svg id="eye-password" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Confirm New Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="h-9 w-full rounded border border-gray-300 bg-white px-2 pr-9 text-sm focus:border-gray-400 focus:outline-none"
                        placeholder="••••••••" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                        class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 hover:text-gray-600">
                        <svg id="eye-confirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded border border-blue-500 bg-blue-300 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-blue-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update User
            </button>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded border border-gray-300 bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.innerHTML = isHidden
        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`
        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
}
</script>
@endpush
@endsection