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

    <form method="GET" action="{{ route('fg.storage.receiving') }}" class="rounded border border-gray-200 bg-gray-100 px-4 py-3">
        <div class="mb-2 flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Date Filter</span>

            <select class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm">
                <option>Date Injection</option>
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
                <option value="lot_no" {{ ($filters['search_by'] ?? '') === 'lot_no' ? 'selected' : '' }}>Lot No</option>
                <option value="label_id" {{ ($filters['search_by'] ?? '') === 'label_id' ? 'selected' : '' }}>Label ID</option>
            </select>

            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="Input Keyword"
                class="h-9 w-44 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="submit" class="h-9 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-800 hover:bg-yellow-500">
                Search
            </button>
            <a href="{{ route('fg.storage.receiving') }}"
                class="h-9 rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700">
                Reset
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Total Row</span>
            <input type="text" readonly value="{{ $summary['total_row'] ?? 0 }}" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page Size</span>
            <input type="number" min="1" max="100" name="page_size" value="{{ $filters['page_size'] ?? 10 }}"
                class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page No</span>
            <input type="number" min="1" name="page" value="{{ $scans->currentPage() }}"
                class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="submit" class="h-7 rounded bg-yellow-400 px-2 text-xs text-gray-800">Apply</button>
            <div class="ml-3 text-xs text-gray-600">
                Total Qty Scan : <strong>{{ number_format($summary['total_qty'] ?? 0) }}</strong>
            </div>
        </div>
    </form>

    <div class="flex flex-wrap gap-1 text-sm">
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Incoming</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Request</a>
        <a href="{{ route('fg.storage.receiving') }}" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 font-semibold text-gray-800">FG Receiving</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Return</a>
        <a href="{{ route('fg.storage') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Delivery</a>
        <a href="{{ route('fg.storage.stock') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Stock</a>
        <a href="{{ route('fg.storage.swa') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG for SWA</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG OnHold</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Dispose</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Customer Return</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Stock</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Delivery</a>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('fg.storage.receiving.create-unregistered') }}" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Create</a>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">FG OnHold</button>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Dispose</button>
        <a href="{{ route('fg.storage.receiving.create-unregistered') }}" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">
            Create Unregistered
        </a>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Create FG Return</button>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Create Unregistered QR</button>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Dispose QR</button>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">FG OnHold QR</button>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Create FG Return QR</button>
    </div>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="w-8 border border-yellow-300 px-2 py-2 text-center">#</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Injection Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Part Code</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Part Name</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">RFID Decimal Tag</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Lot No</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Qty</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Delivery</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Delivery Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">QC Status</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Receive By</th>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="border border-yellow-200 px-3 py-4 text-right text-gray-500">
                            No records to view
                        </td>
                    </tr>
                @endforelse

                @if ($scans->count() > 0)
                    <tr>
                        <td colspan="13" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ $scans->onFirstPage() ? '#' : $scans->url(1) }}"
                                    class="rounded border px-1 {{ $scans->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">
                                    &laquo;
                                </a>
                                <a href="{{ $scans->onFirstPage() ? '#' : $scans->previousPageUrl() }}"
                                    class="rounded border px-1 {{ $scans->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">
                                    &lsaquo;
                                </a>
                                <span>Page {{ $scans->currentPage() }} of {{ $scans->lastPage() }}</span>
                                <a href="{{ $scans->hasMorePages() ? $scans->nextPageUrl() : '#' }}"
                                    class="rounded border px-1 {{ $scans->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
                                    &rsaquo;
                                </a>
                                <a href="{{ $scans->hasMorePages() ? $scans->url($scans->lastPage()) : '#' }}"
                                    class="rounded border px-1 {{ $scans->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
                                    &raquo;
                                </a>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Delete</button>
    </div>
</div>
@endsection
