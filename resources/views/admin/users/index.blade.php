@extends('layouts.app')

@section('title', 'User Management')

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

    <h1 class="text-center text-2xl font-semibold text-gray-700">User Management</h1>

    @if (session('success'))
    <div class="rounded border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}" class="rounded border border-gray-200 bg-gray-50 px-4 py-4">
        @csrf
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Role</label>
                <select name="role"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    @foreach ($availableRoles as $roleValue => $roleLabel)
                    <option value="{{ $roleValue }}" {{ old('role') === $roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Password</label>
                <input type="password" name="password"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-yellow-400">
                Add User
            </button>
        </div>
    </form>

    <form method="GET" action="{{ route('admin.users.index') }}" class="rounded border border-gray-200 bg-gray-100 px-4 py-3">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-gray-600">Search</span>
            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="Name / Username"
                class="h-9 w-56 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="text-gray-600">Role</span>
            <select name="role" class="h-9 rounded border border-gray-300 bg-white px-2 text-sm">
                <option value="">All</option>
                @foreach ($availableRoles as $roleValue => $roleLabel)
                <option value="{{ $roleValue }}" {{ ($filters['role'] ?? '') === $roleValue ? 'selected' : '' }}>
                    {{ $roleLabel }}
                </option>
                @endforeach
            </select>

            <span class="text-gray-600">Page Size</span>
            <input type="number" min="1" max="100" name="page_size" value="{{ $filters['page_size'] ?? 10 }}"
                class="h-9 w-20 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="submit" class="h-9 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-900 hover:bg-yellow-500">
                Search
            </button>
            <a href="{{ route('admin.users.index') }}" class="h-9 rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                Reset
            </a>
        </div>
    </form>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="w-8 border border-yellow-300 px-2 py-2 text-center">#</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Name</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Username</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Role</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Status</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                @forelse ($users as $adminUser)
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">{{ ($users->firstItem() ?? 0) + $loop->index }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $adminUser->name }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $adminUser->username }}</td>
                    <td class="border border-yellow-200 px-2 py-1 font-semibold">{{ $adminUser->role_label }}</td>
                    <td class="border border-yellow-200 px-2 py-1 text-center">
                        <span class="rounded px-2 py-1 text-[11px] font-semibold {{ $adminUser->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($adminUser->status) }}
                        </span>
                        @if ($adminUser->status === 'inactive')
                        <div class="mt-1 text-[10px] text-red-600 font-semibold">
                            Account Inactive
                        </div>
                        @endif
                    </td>
                    <td class="border border-yellow-200 px-2 py-1 text-center">
                        <div class="flex justify-center gap-2">
                            <button type="button" title="Edit User"
                                data-update-url="{{ route('admin.users.update', $adminUser) }}"
                                data-name="{{ $adminUser->name }}"
                                data-username="{{ $adminUser->username }}"
                                data-role="{{ $adminUser->role }}"
                                onclick="openEditModal(this)"
                                class="inline-flex items-center justify-center rounded border border-blue-300 bg-blue-50 p-1.5 text-blue-600 hover:bg-blue-100 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('admin.users.destroy', $adminUser) }}" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete User" class="inline-flex items-center justify-center rounded border border-gray-400 bg-gray-100 p-1.5 text-gray-600 hover:bg-gray-200 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @if ($adminUser->status === 'active')
                            <form method="POST" action="{{ route('admin.users.deactivate', $adminUser) }}" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this account?')">
                                @csrf
                                <button type="submit" title="Deactivate Account" class="inline-flex items-center justify-center rounded border border-red-300 bg-red-50 p-1.5 text-red-600 hover:bg-red-100 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.users.activate', $adminUser) }}" class="inline" onsubmit="return confirm('Are you sure you want to activate this account?')">
                                @csrf
                                <button type="submit" title="Activate Account" class="inline-flex items-center justify-center rounded border border-green-300 bg-green-50 p-1.5 text-green-600 hover:bg-green-100 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="border border-yellow-200 px-3 py-3 text-center text-gray-500">
                        No users registered yet.
                    </td>
                </tr>
                @endforelse

                @if ($users->count() > 0)
                <tr>
                    <td colspan="6" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ $users->onFirstPage() ? '#' : $users->url(1) }}"
                                class="rounded border px-1 {{ $users->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                &laquo;
                            </a>
                            <a href="{{ $users->onFirstPage() ? '#' : $users->previousPageUrl() }}"
                                class="rounded border px-1 {{ $users->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                &lsaquo;
                            </a>
                            <span>Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
                            <a href="{{ $users->hasMorePages() ? $users->nextPageUrl() : '#' }}"
                                class="rounded border px-1 {{ $users->hasMorePages() ? 'bg-white text-gray-700 hover:bg-gray-50' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
                                &rsaquo;
                            </a>
                            <a href="{{ $users->hasMorePages() ? $users->url($users->lastPage()) : '#' }}"
                                class="rounded border px-1 {{ $users->hasMorePages() ? 'bg-white text-gray-700 hover:bg-gray-50' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
                                &raquo;
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
{{-- Edit Modal --}}
<div id="editModal" style="display:none;" class="fixed inset-0 z-50 bg-black bg-opacity-50 overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-lg rounded border border-gray-300 bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                <h3 class="font-semibold text-gray-700">Edit User</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editModal-form" method="POST"
                action="{{ ($errors->any() && old('_edit_url')) ? old('_edit_url') : '#' }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_edit_url" id="editModalUrl" value="{{ old('_edit_url', '') }}">
                <div class="grid grid-cols-1 gap-3 px-4 py-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Name</label>
                        <input type="text" name="name" id="modal-name" value="{{ old('name', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Username</label>
                        <input type="text" id="modal-username" readonly
                            class="h-9 w-full rounded border border-gray-300 bg-gray-100 px-2 text-sm text-gray-500 cursor-not-allowed">
                        <input type="hidden" name="username" id="modal-username-hidden" value="{{ old('username', '') }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Role</label>
                        <select name="role" id="modal-role"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                            @foreach ($availableRoles as $roleValue => $roleLabel)
                            <option value="{{ $roleValue }}" {{ old('role') === $roleValue ? 'selected' : '' }}>
                                {{ $roleLabel }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">New Password <span class="normal-case font-normal text-gray-400">(optional)</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="modal-password"
                                class="h-9 w-full rounded border border-gray-300 bg-white px-2 pr-9 text-sm focus:border-gray-400 focus:outline-none"
                                placeholder="••••••••" autocomplete="new-password">
                            <button type="button" onclick="toggleModalPassword('modal-password', 'modal-eye-password')"
                                class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 hover:text-gray-600">
                                <svg id="modal-eye-password" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="modal-password-confirm"
                                class="h-9 w-full rounded border border-gray-300 bg-white px-2 pr-9 text-sm focus:border-gray-400 focus:outline-none"
                                placeholder="••••••••" autocomplete="new-password">
                            <button type="button" onclick="toggleModalPassword('modal-password-confirm', 'modal-eye-confirm')"
                                class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 hover:text-gray-600">
                                <svg id="modal-eye-confirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @if ($errors->any() && old('_edit_url'))
                <div class="mx-4 mb-3 rounded border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
                    {{ $errors->first() }}
                </div>
                @endif
                <div class="flex gap-2 border-t border-gray-200 px-4 py-3">
                    <button type="submit" class="rounded border border-blue-500 bg-blue-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-blue-400">
                        Update User
                    </button>
                    <button type="button" onclick="closeEditModal()" class="rounded border border-gray-300 bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const editModal = document.getElementById('editModal');
const editForm = document.getElementById('editModal-form');

function openEditModal(btn) {
    const d = btn.dataset;
    editForm.action = d.updateUrl;
    document.getElementById('editModalUrl').value = d.updateUrl;
    document.getElementById('modal-name').value = d.name || '';
    document.getElementById('modal-username').value = d.username || '';
    document.getElementById('modal-username-hidden').value = d.username || '';
    document.getElementById('modal-role').value = d.role || '';
    document.getElementById('modal-password').value = '';
    document.getElementById('modal-password-confirm').value = '';
    editModal.style.display = 'block';
    gapuroLockScroll();
}

function closeEditModal() {
    editModal.style.display = 'none';
    gapuroUnlockScroll();
}

function toggleModalPassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.innerHTML = isHidden
        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`
        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
}

editModal.addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

@if($errors->any() && old('_edit_url'))
editModal.style.display = 'block';
gapuroLockScroll();
@endif
</script>
@endpush
@endsection