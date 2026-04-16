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

    <form method="GET" action="{{ route('fg.storage.swa') }}" class="rounded border border-gray-200 bg-gray-100 px-4 py-3">
        <input type="hidden" name="sort_by" value="{{ $currentSortBy }}">
        <input type="hidden" name="sort_dir" value="{{ $currentSortDir }}">

        <div class="mb-2 flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Date Filter</span>

            <select name="date_filter" class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm">
                <option value="created_at" {{ ($filters['date_filter'] ?? '') === 'created_at' ? 'selected' : '' }}>
                    Injection Date
                </option>
            </select>

            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                class="h-9 rounded border border-gray-300 bg-white px-2 text-sm">
            <span class="text-xs text-gray-500">To</span>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                class="h-9 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-4 text-gray-600">Search By</span>

            <select name="search_by" class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm">
                <option value="">Search By</option>
                <option value="part_code" {{ ($filters['search_by'] ?? '') === 'part_code' ? 'selected' : '' }}>Part Code</option>
                <option value="part_name" {{ ($filters['search_by'] ?? '') === 'part_name' ? 'selected' : '' }}>Part Name</option>
                <option value="start_lot_no" {{ ($filters['search_by'] ?? '') === 'start_lot_no' ? 'selected' : '' }}>Start Lot No</option>
                <option value="end_lot_no" {{ ($filters['search_by'] ?? '') === 'end_lot_no' ? 'selected' : '' }}>End Lot No</option>
            </select>

            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="Input Keyword"
                class="h-9 w-44 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="submit" class="h-9 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-900 hover:bg-yellow-500">
                Search
            </button>
            <a href="{{ route('fg.storage.swa') }}" class="h-9 rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700">
                Reset
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Total Row</span>
            <input type="text" readonly value="{{ $plans->total() }}"
                class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page Size</span>
            <input type="number" min="1" max="100" name="page_size" value="{{ $filters['page_size'] ?? 10 }}"
                class="h-7 w-14 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page No</span>
            <input type="number" min="1" name="page" value="{{ $plans->currentPage() }}"
                class="h-7 w-14 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="submit" class="h-7 rounded bg-yellow-400 px-2 text-xs text-gray-800">Apply</button>
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
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Incoming</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Request</a>
        <a href="{{ route('fg.storage.receiving') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Receiving</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Return</a>
        <a href="{{ route('fg.storage') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Delivery</a>
        <a href="{{ route('fg.storage.stock') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Stock</a>
        <a href="{{ route('fg.storage.swa') }}" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 font-semibold text-gray-800">FG for SWA</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG OnHold</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Dispose</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Customer Return</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Stock</a>
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
                                    <a href="{{ route('fg.storage.swa.edit', $plan) }}"
                                        class="rounded border border-blue-300 bg-blue-50 px-2 py-1 text-[11px] text-blue-700">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('fg.storage.swa.destroy', $plan) }}" onsubmit="return confirm('Hapus plan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded border border-red-300 bg-red-50 px-2 py-1 text-[11px] text-red-700">
                                            Delete
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
@endsection
