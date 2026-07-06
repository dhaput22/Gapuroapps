@extends('layouts.app')

@section('title', 'Operator')

@section('content')
<div class="space-y-4 text-[13px] text-gray-700">
    <div class="flex items-center justify-between border border-gray-200 bg-gray-100 px-4 py-2">
        <p class="font-semibold">
            Current Period : <span class="font-medium">2025-10-01 - 2025-12-31</span>
        </p>
        <p class="text-sm">
            <span class="font-semibold">Master Data</span>
            <span class="mx-2 text-gray-400">|</span>
            Operator
        </p>
    </div>

    <h1 class="text-center text-2xl font-semibold text-gray-700">Master Operator</h1>

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

    @php
    $currentSortBy = $filters['sort_by'] ?? 'created_at';
    $currentSortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

    $sortUrl = static function (string $column) use ($filters, $currentSortBy, $currentSortDir): string {
    $nextDirection = $currentSortBy === $column && $currentSortDir === 'asc' ? 'desc' : 'asc';

    $query = [
    'keyword' => $filters['keyword'] ?? null,
    'page_size' => $filters['page_size'] ?? null,
    'sort_by' => $column,
    'sort_dir' => $nextDirection,
    ];

    $query = array_filter($query, static fn($value) => $value !== null && $value !== '');

    return route('operators.index', $query);
    };
    @endphp

    <form method="POST" action="{{ route('operators.store') }}" class="rounded border border-gray-200 bg-gray-50 px-4 py-4">
        @csrf
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">ID Number</label>
                <input type="text" name="employee_id" value="{{ old('employee_id') }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Department</label>
                <input type="text" name="department" value="{{ old('department') }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 text-sm font-semibold text-gray-800">
                Add Operator
            </button>
        </div>
    </form>

    <form method="GET" action="{{ route('operators.index') }}" class="rounded border border-gray-200 bg-gray-100 px-4 py-3">
        <input type="hidden" name="sort_by" value="{{ $currentSortBy }}">
        <input type="hidden" name="sort_dir" value="{{ $currentSortDir }}">

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-gray-600">Search</span>
            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="ID / Name / Department"
                class="h-9 w-56 rounded border border-gray-300 bg-white px-2 text-sm">
            <span class="text-gray-600">Page Size</span>
            <input type="number" min="1" max="100" name="page_size" value="{{ $filters['page_size'] ?? 10 }}"
                class="h-9 w-20 rounded border border-gray-300 bg-white px-2 text-sm">
            <button type="submit" class="h-9 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-900 hover:bg-yellow-500">
                Search
            </button>
            <a href="{{ route('operators.index') }}" class="h-9 rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700">
                Reset
            </a>
        </div>
    </form>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="w-8 border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('created_at') }}" class="hover:underline">#</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('employee_id') }}" class="hover:underline">ID Number</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('name') }}" class="hover:underline">Name</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('department') }}" class="hover:underline">Department</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                @forelse ($operators as $operator)
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">{{ ($operators->firstItem() ?? 0) + $loop->index }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $operator->employee_id }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $operator->name }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $operator->department }}</td>
                    <td class="border border-yellow-200 px-2 py-1 text-center">
                        <div class="flex justify-center gap-2">
                            <button type="button" title="Edit Operator"
                                data-update-url="{{ route('operators.update', $operator) }}"
                                data-employee-id="{{ $operator->employee_id }}"
                                data-name="{{ $operator->name }}"
                                data-department="{{ $operator->department }}"
                                onclick="openEditModal(this)"
                                class="inline-flex items-center justify-center rounded border border-blue-300 bg-blue-50 p-1.5 text-blue-600 hover:bg-blue-100 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('operators.destroy', $operator) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this plan? Permanently delete this operator? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Operator" class="inline-flex items-center justify-center rounded border border-gray-400 bg-gray-100 p-1.5 text-gray-600 hover:bg-gray-200 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="border border-yellow-200 px-3 py-3 text-center text-gray-500">
                        Belum ada data operator.
                    </td>
                </tr>
                @endforelse

                @if ($operators->count() > 0)
                <tr>
                    <td colspan="5" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ $operators->onFirstPage() ? '#' : $operators->url(1) }}"
                                class="rounded border px-1 {{ $operators->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">
                                &laquo;
                            </a>
                            <a href="{{ $operators->onFirstPage() ? '#' : $operators->previousPageUrl() }}"
                                class="rounded border px-1 {{ $operators->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">
                                &lsaquo;
                            </a>
                            <span>Page {{ $operators->currentPage() }} of {{ $operators->lastPage() }}</span>
                            <a href="{{ $operators->hasMorePages() ? $operators->nextPageUrl() : '#' }}"
                                class="rounded border px-1 {{ $operators->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
                                &rsaquo;
                            </a>
                            <a href="{{ $operators->hasMorePages() ? $operators->url($operators->lastPage()) : '#' }}"
                                class="rounded border px-1 {{ $operators->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
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
        <div class="w-full max-w-md rounded border border-gray-300 bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                <h3 class="font-semibold text-gray-700">Edit Operator</h3>
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
                <div class="space-y-3 px-4 py-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Nomor ID</label>
                        <input type="text" name="employee_id" id="modal-employee-id" value="{{ old('employee_id', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Nama</label>
                        <input type="text" name="name" id="modal-name" value="{{ old('name', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Departemen</label>
                        <input type="text" name="department" id="modal-department" value="{{ old('department', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                </div>
                @if ($errors->any() && old('_edit_url'))
                <div class="mx-4 mb-3 rounded border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
                    {{ $errors->first() }}
                </div>
                @endif
                <div class="flex gap-2 border-t border-gray-200 px-4 py-3">
                    <button type="submit" class="rounded border border-yellow-500 bg-yellow-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-yellow-400">
                        Update Operator
                    </button>
                    <button type="button" onclick="closeEditModal()" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        Batal
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
    document.getElementById('modal-employee-id').value = d.employeeId || '';
    document.getElementById('modal-name').value = d.name || '';
    document.getElementById('modal-department').value = d.department || '';
    editModal.style.display = 'block';
    gapuroLockScroll();
}

function closeEditModal() {
    editModal.style.display = 'none';
    gapuroUnlockScroll();
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