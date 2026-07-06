@extends('layouts.app')

@section('title', 'FG Receiving')

@section('content')
<div class="space-y-4 text-[13px] text-gray-700">
    <div class="flex items-center justify-between border border-gray-200 bg-gray-100 px-4 py-2">
        <p class="font-semibold">
            Current Period : <span class="font-medium">2025-10-01 - 2025-12-31</span>
        </p>
        <p class="text-sm">
            <span class="font-semibold">FG Storage</span>
            <span class="mx-2 text-gray-400">|</span>
            Form No : <span class="font-semibold">329</span>
        </p>
    </div>

    <h1 class="text-center text-2xl font-semibold text-gray-700">FG Storage</h1>

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
    $currentSortBy = $filters['sort_by'] ?? 'scanned_at';
    $currentSortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

    $sortUrl = static function (string $column) use ($filters, $currentSortBy, $currentSortDir): string {
    $nextDirection = $currentSortBy === $column && $currentSortDir === 'asc' ? 'desc' : 'asc';

    $query = [
    'date_from' => $filters['date_from'] ?? null,
    'date_to' => $filters['date_to'] ?? null,
    'search_by' => $filters['search_by'] ?? null,
    'keyword' => $filters['keyword'] ?? null,
    'page_size' => $filters['page_size'] ?? null,
    'sort_by' => $column,
    'sort_dir' => $nextDirection,
    ];

    $query = array_filter($query, static fn($value) => $value !== null && $value !== '');

    return route('fg.storage.receiving', $query);
    };
    @endphp

    @php
        $dtFrom = $filters['date_from'] ?? '';
        if (strlen($dtFrom) === 10) $dtFrom .= 'T00:00';
        $dtTo = $filters['date_to'] ?? '';
        if (strlen($dtTo) === 10) $dtTo .= 'T23:59';
    @endphp
    <form method="GET" action="{{ route('fg.storage.receiving') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
        <input type="hidden" name="sort_by" value="{{ $currentSortBy }}">
        <input type="hidden" name="sort_dir" value="{{ $currentSortDir }}">

        <div class="mb-2 flex flex-wrap items-center gap-2">
            <span class="min-w-[80px] text-sm font-medium text-gray-600">Date Filter</span>

            <input type="datetime-local" name="date_from" value="{{ $dtFrom }}"
                class="h-9 rounded border border-gray-300 bg-white px-2 text-sm focus:border-yellow-400 focus:outline-none focus:ring-1 focus:ring-yellow-300">

            <span class="text-xs font-medium text-gray-400">To</span>

            <input type="datetime-local" name="date_to" value="{{ $dtTo }}"
                class="h-9 rounded border border-gray-300 bg-white px-2 text-sm focus:border-yellow-400 focus:outline-none focus:ring-1 focus:ring-yellow-300">

            <div class="mx-1 h-5 w-px bg-gray-200"></div>

            <select name="search_by" class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm font-medium focus:outline-none">
                <option value="">Search By</option>
                <option value="part_code" {{ ($filters['search_by'] ?? '') === 'part_code' ? 'selected' : '' }}>Part Code</option>
                <option value="part_name" {{ ($filters['search_by'] ?? '') === 'part_name' ? 'selected' : '' }}>Part Name</option>
                <option value="lot_no" {{ ($filters['search_by'] ?? '') === 'lot_no' ? 'selected' : '' }}>Lot No</option>
                <option value="label_id" {{ ($filters['search_by'] ?? '') === 'label_id' ? 'selected' : '' }}>Label ID</option>
            </select>

            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="Input Keyword"
                class="h-9 w-44 rounded border border-gray-300 bg-white px-2 text-sm focus:border-yellow-400 focus:outline-none focus:ring-1 focus:ring-yellow-300">

            <button type="submit" class="h-9 rounded bg-yellow-400 px-4 text-xs font-semibold text-gray-800 hover:bg-yellow-500">
                Search
            </button>
            <a href="{{ route('fg.storage.receiving') }}"
                class="h-9 rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">
                Reset
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-2">
            <span class="text-xs text-gray-500">Total Row</span>
            <input type="text" readonly value="{{ $summary['total_row'] ?? 0 }}" class="h-7 w-14 rounded border border-gray-200 bg-gray-50 px-2 text-center text-xs font-semibold text-gray-700">

            <span class="text-xs text-gray-500">Page Size</span>
            <input type="number" min="1" max="100" name="page_size" value="{{ $filters['page_size'] ?? 10 }}"
                class="h-7 w-14 rounded border border-gray-300 bg-white px-2 text-center text-xs focus:outline-none">

            <span class="text-xs text-gray-500">Page No</span>
            <input type="number" min="1" name="page" value="{{ $scans->currentPage() }}"
                class="h-7 w-14 rounded border border-gray-300 bg-white px-2 text-center text-xs focus:outline-none">

            <button type="submit" class="h-7 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-800 hover:bg-yellow-500">Apply</button>
            <div class="text-xs text-gray-500">
                Total Qty : <strong class="text-gray-700">{{ number_format($summary['total_qty'] ?? 0) }}</strong>
            </div>
        </div>
    </form>

    <div class="flex flex-wrap gap-1 text-sm">
        <a href="{{ route('fg.storage.receiving') }}" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 font-semibold text-gray-800">FG Receiving</a>
        <a href="{{ route('fg.storage.return.index') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Return</a>
        <a href="{{ route('fg.storage') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Delivery</a>
        <a href="{{ route('fg.storage.stock') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Stock</a>
        <a href="{{ route('fg.storage.swa') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG for SWA</a>
        <a href="{{ route('fg.storage.dispose.index') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Dispose</a>
        <a href="{{ route('fg.storage.summary-stock') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Stock</a>
        <a href="{{ route('fg.storage.summary-delivery') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Delivery</a>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('fg.storage.receiving.create-unregistered') }}" class="inline-flex items-center gap-1.5 rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800 hover:bg-yellow-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Unregistered
        </a>
    </div>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="w-8 border border-yellow-300 px-2 py-2 text-center">No</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('scanned_at') }}" class="hover:underline">Injection Date</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('part_code') }}" class="hover:underline">Part Code</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('part_name') }}" class="hover:underline">Part Name</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">RFID Decimal Tag</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('lot_no') }}" class="hover:underline">Lot No</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('qty_box') }}" class="hover:underline">Qty</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Storage</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('scanned_at') }}" class="hover:underline">Storage Date</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Delivery</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Delivery Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">QC Status</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Receive By</th>
                    @if(auth()->user()?->isAdmin())
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                @forelse ($scans as $scan)
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">{{ ($scans->firstItem() ?? 0) + $loop->index }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ optional($scan->scanned_at)->format('Y-m-d') ?? optional($scan->created_at)->format('Y-m-d') }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->part_code }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->part_name }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->label_id ?: '-' }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->lot_no }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ number_format((int) $scan->qty_box) }}</td>
                    <td class="border border-yellow-200 px-2 py-1">2nd Floor</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ optional($scan->scanned_at)->format('Y-m-d') ?? optional($scan->created_at)->format('Y-m-d') }}</td>
                    <td class="border border-yellow-200 px-2 py-1">-</td>
                    <td class="border border-yellow-200 px-2 py-1">-</td>
                    <td class="border border-yellow-200 px-2 py-1">-</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->operator ? ($scan->operator->name) : '-' }}</td>
                    @if(auth()->user()?->isAdmin())
                    <td class="border border-yellow-200 px-2 py-1 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" title="Edit"
                                data-update-url="{{ route('fg.storage.receiving.update', $scan) }}"
                                data-part-code="{{ $scan->part_code }}"
                                data-part-name="{{ $scan->part_name ?? '' }}"
                                data-lot-no="{{ $scan->lot_no }}"
                                data-qty-box="{{ $scan->qty_box }}"
                                data-scanned-at="{{ optional($scan->scanned_at)->format('Y-m-d') }}"
                                data-operator-id="{{ $scan->operator_id ?? '' }}"
                                onclick="openEditModal(this)"
                                class="inline-flex items-center justify-center rounded border border-blue-300 bg-blue-50 p-1.5 text-blue-600 hover:bg-blue-100 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('fg.storage.receiving.destroy', $scan) }}"
                                onsubmit="return confirm('Are you sure you want to delete this data?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus" class="inline-flex items-center justify-center rounded border border-red-300 bg-red-50 p-1.5 text-red-600 hover:bg-red-100 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()?->isAdmin() ? 14 : 13 }}" class="border border-yellow-200 px-3 py-4 text-center text-gray-500">
                        No records to view
                    </td>
                </tr>
                @endforelse

                @if ($scans->count() > 0)
                <tr>
                    <td colspan="{{ auth()->user()?->isAdmin() ? 14 : 13 }}" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                        <div class="grid grid-cols-[1fr_auto_1fr] items-center">
                            <div></div>
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ $scans->onFirstPage() ? '#' : $scans->url(1) }}" class="rounded border px-1 {{ $scans->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">&laquo;</a>
                                <a href="{{ $scans->onFirstPage() ? '#' : $scans->previousPageUrl() }}" class="rounded border px-1 {{ $scans->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">&lsaquo;</a>
                                <span>Page {{ $scans->currentPage() }} of {{ $scans->lastPage() }}</span>
                                <a href="{{ $scans->hasMorePages() ? $scans->nextPageUrl() : '#' }}" class="rounded border px-1 {{ $scans->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">&rsaquo;</a>
                                <a href="{{ $scans->hasMorePages() ? $scans->url($scans->lastPage()) : '#' }}" class="rounded border px-1 {{ $scans->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">&raquo;</a>
                            </div>
                            <div class="pr-1 text-right text-gray-600">
                                Total : {{ number_format($scans->total()) }}
                            </div>
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
                <h3 class="font-semibold text-gray-700">Edit FG Receiving</h3>
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
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Part Code</label>
                        <input type="text" name="part_code" id="modal-part-code" value="{{ old('part_code', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Part Name</label>
                        <input type="text" name="part_name" id="modal-part-name" value="{{ old('part_name', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Lot No</label>
                        <input type="text" name="lot_no" id="modal-lot-no" value="{{ old('lot_no', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Qty Box</label>
                        <input type="number" name="qty_box" id="modal-qty-box" value="{{ old('qty_box', '') }}" min="0"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Injection Date</label>
                        <input type="date" name="scanned_at" id="modal-scanned-at" value="{{ old('scanned_at', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Operator</label>
                        <select name="operator_id" id="modal-operator-id"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                            <option value="">— Pilih Operator —</option>
                            @foreach ($operators as $op)
                            <option value="{{ $op->id }}" {{ old('operator_id') == $op->id ? 'selected' : '' }}>
                                {{ $op->employee_id }} - {{ $op->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if ($errors->any() && old('_edit_url'))
                <div class="mx-4 mb-3 rounded border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
                    {{ $errors->first() }}
                </div>
                @endif
                <div class="flex gap-2 border-t border-gray-200 px-4 py-3">
                    <button type="submit" class="rounded border border-yellow-500 bg-yellow-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-yellow-400">
                        Simpan
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
    document.getElementById('modal-part-code').value = d.partCode || '';
    document.getElementById('modal-part-name').value = d.partName || '';
    document.getElementById('modal-lot-no').value = d.lotNo || '';
    document.getElementById('modal-qty-box').value = d.qtyBox || '';
    document.getElementById('modal-scanned-at').value = d.scannedAt || '';
    document.getElementById('modal-operator-id').value = d.operatorId || '';
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