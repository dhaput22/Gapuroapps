@extends('layouts.app')

@section('title', 'FG for SWA')

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
    $currentSortBy = $filters['sort_by'] ?? 'created_at';
    $currentSortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
    $canManageWarehouseData = auth()->user()?->canManageWarehouseData() ?? false;

    $sortUrl = static function (string $column) use ($filters, $currentSortBy, $currentSortDir): string {
    $nextDirection = $currentSortBy === $column && $currentSortDir === 'asc' ? 'desc' : 'asc';

    $query = [
    'date_filter' => $filters['date_filter'] ?? null,
    'date_from' => $filters['date_from'] ?? null,
    'date_to' => $filters['date_to'] ?? null,
    'search_by' => $filters['search_by'] ?? null,
    'keyword' => $filters['keyword'] ?? null,
    'page_size' => $filters['page_size'] ?? null,
    'sort_by' => $column,
    'sort_dir' => $nextDirection,
    ];

    $query = array_filter($query, static fn($value) => $value !== null && $value !== '');

    return route('fg.storage.swa', $query);
    };
    @endphp

    @php
    $dtFrom = $filters['date_from'] ?? '';
    if (strlen($dtFrom) === 10) $dtFrom .= 'T00:00';
    $dtTo = $filters['date_to'] ?? '';
    if (strlen($dtTo) === 10) $dtTo .= 'T23:59';
    @endphp
    <form method="GET" action="{{ route('fg.storage.swa') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
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
                <option value="start_lot_no" {{ ($filters['search_by'] ?? '') === 'start_lot_no' ? 'selected' : '' }}>Start Lot No</option>
                <option value="end_lot_no" {{ ($filters['search_by'] ?? '') === 'end_lot_no' ? 'selected' : '' }}>End Lot No</option>
            </select>

            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="Input Keyword"
                class="h-9 w-44 rounded border border-gray-300 bg-white px-2 text-sm focus:border-yellow-400 focus:outline-none focus:ring-1 focus:ring-yellow-300">

            <button type="submit" class="h-9 rounded bg-yellow-400 px-4 text-xs font-semibold text-gray-800 hover:bg-yellow-500">
                Search
            </button>
            <a href="{{ route('fg.storage.swa') }}" class="h-9 rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">
                Reset
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-2">
            <span class="text-xs text-gray-500">Total Row</span>
            <input type="text" readonly value="{{ $plans->total() }}"
                class="h-7 w-14 rounded border border-gray-200 bg-gray-50 px-2 text-center text-xs font-semibold text-gray-700">

            <span class="text-xs text-gray-500">Page Size</span>
            <input type="number" min="1" max="100" name="page_size" value="{{ $filters['page_size'] ?? 10 }}"
                class="h-7 w-14 rounded border border-gray-300 bg-white px-2 text-center text-xs focus:outline-none">

            <span class="text-xs text-gray-500">Page No</span>
            <input type="number" min="1" name="page" value="{{ $plans->currentPage() }}"
                class="h-7 w-14 rounded border border-gray-300 bg-white px-2 text-center text-xs focus:outline-none">

            <button type="submit" class="h-7 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-800 hover:bg-yellow-500">Apply</button>
        </div>
    </form>

    <div class="flex flex-wrap items-center justify-between gap-3 rounded border border-gray-200 bg-gray-100 px-4 py-3">
        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600">
            <span>Total Plan Row: <strong>{{ $summary['plan_count'] ?? 0 }}</strong></span>
            <span>Total Plan Terdaftar: <strong>{{ number_format($summary['total_plan_registered'] ?? 0) }}</strong></span>
            <span>Total Scan (FG Receiving): <strong>{{ number_format($summary['total_scan_registered'] ?? 0) }}</strong></span>
        </div>

        <a href="{{ route('fg.storage.swa.create') }}"
            class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 text-sm font-semibold text-gray-800">
            Create/Register Plan
        </a>
    </div>

    <div class="flex flex-wrap gap-1 text-sm">
        <a href="{{ route('fg.storage.receiving') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Receiving</a>
        <a href="{{ route('fg.storage.return.index') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Return</a>
        <a href="{{ route('fg.storage') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Delivery</a>
        <a href="{{ route('fg.storage.stock') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Stock</a>
        <a href="{{ route('fg.storage.swa') }}" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 font-semibold text-gray-800">FG for SWA</a>
        <a href="{{ route('fg.storage.dispose.index') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Dispose</a>
        <a href="{{ route('fg.storage.summary-stock') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Stock</a>
        <a href="{{ route('fg.storage.summary-delivery') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Delivery</a>
    </div>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="w-8 border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('created_at') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>#</span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('part_code') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>Part Code</span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('part_name') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>Part Name</span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('start_lot_no') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>Start Lot No</span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('end_lot_no') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>End Lot No</span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('qty_box') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>Qty Box</span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Total Scan</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">
                        <a href="{{ $sortUrl('total_plan') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>Total Plan</span>
                        </a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Gap</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Result</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                @forelse ($plans as $plan)
                @php
                $isMatch = (int) $plan->total_scan === (int) $plan->total_plan;
                @endphp
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">{{ ($plans->firstItem() ?? 0) + $loop->index }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $plan->part_code }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $plan->part_name }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $plan->start_lot_no }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $plan->end_lot_no }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ number_format((int) $plan->qty_box) }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ number_format((int) $plan->total_scan) }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ number_format((int) $plan->total_plan) }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ number_format((int) $plan->total_plan - (int) $plan->total_scan) }}</td>
                    <td class="border border-yellow-200 px-2 py-1 font-semibold {{ $isMatch ? 'text-green-700' : 'text-red-700' }}">
                        {{ $isMatch ? 'OK' : 'NO MATCH' }}
                    </td>
                    <td class="border border-yellow-200 px-2 py-1 text-center">
                        @if ($canManageWarehouseData)
                        <div class="inline-flex items-center gap-1">
                            <button type="button" title="Edit"
                                data-update-url="{{ route('fg.storage.swa.update', $plan) }}"
                                data-part-code="{{ $plan->part_code }}"
                                data-part-name="{{ $plan->part_name }}"
                                data-start-lot-no="{{ $plan->start_lot_no }}"
                                data-end-lot-no="{{ $plan->end_lot_no }}"
                                data-qty-box="{{ $plan->qty_box }}"
                                data-total-plan="{{ $plan->total_plan }}"
                                onclick="openEditModal(this)"
                                class="inline-flex items-center justify-center rounded border border-blue-300 bg-blue-50 p-1.5 text-blue-600 hover:bg-blue-100 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('fg.storage.swa.destroy', $plan) }}" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus"
                                    class="inline-flex items-center justify-center rounded border border-red-300 bg-red-50 p-1.5 text-red-600 hover:bg-red-100 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-[11px] text-gray-500">View only</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="border border-yellow-200 px-3 py-3 text-center text-gray-500">
                        Belum ada plan SWA yang terdaftar. Klik tombol <strong>Create/Register Plan</strong> untuk menambah data.
                    </td>
                </tr>
                @endforelse

                @if ($plans->count() > 0)
                <tr>
                    <td colspan="11" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ $plans->onFirstPage() ? '#' : $plans->url(1) }}"
                                class="rounded border px-1 {{ $plans->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">
                                &laquo;
                            </a>
                            <a href="{{ $plans->onFirstPage() ? '#' : $plans->previousPageUrl() }}"
                                class="rounded border px-1 {{ $plans->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">
                                &lsaquo;
                            </a>
                            <span>Page {{ $plans->currentPage() }} of {{ $plans->lastPage() }}</span>
                            <a href="{{ $plans->hasMorePages() ? $plans->nextPageUrl() : '#' }}"
                                class="rounded border px-1 {{ $plans->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
                                &rsaquo;
                            </a>
                            <a href="{{ $plans->hasMorePages() ? $plans->url($plans->lastPage()) : '#' }}"
                                class="rounded border px-1 {{ $plans->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
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
                <h3 class="font-semibold text-gray-700">Edit Plan FG for SWA</h3>
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
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Start Lot No</label>
                        <input type="text" name="start_lot_no" id="modal-start-lot-no" value="{{ old('start_lot_no', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">End Lot No</label>
                        <input type="text" name="end_lot_no" id="modal-end-lot-no" value="{{ old('end_lot_no', '') }}"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Qty Box</label>
                        <input type="number" name="qty_box" id="modal-qty-box" value="{{ old('qty_box', '') }}" min="1"
                            class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Total Plan</label>
                        <input type="number" name="total_plan" id="modal-total-plan" value="{{ old('total_plan', '') }}" min="1"
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
                        Update Plan
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
        document.getElementById('modal-start-lot-no').value = d.startLotNo || '';
        document.getElementById('modal-end-lot-no').value = d.endLotNo || '';
        document.getElementById('modal-qty-box').value = d.qtyBox || '';
        document.getElementById('modal-total-plan').value = d.totalPlan || '';
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

    @if($errors -> any() && old('_edit_url'))
    editModal.style.display = 'block';
    gapuroLockScroll();
    @endif
</script>
@endpush
@endsection