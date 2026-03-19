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

    <div class="rounded border border-gray-200 bg-gray-100 px-4 py-3">
        <div class="mb-2 flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Date Filter</span>

            <select class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm">
                <option>Date Injection</option>
            </select>

            <input type="date" value="2025-10-26"
                class="h-9 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="text-xs text-gray-500">To</span>

            <input type="date" value="2025-10-26"
                class="h-9 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-4 text-gray-600">Search By</span>

            <select class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm">
                <option>Search By</option>
                <option>Part Code</option>
                <option>Part Name</option>
            </select>

            <input type="text" placeholder="Input Keyword"
                class="h-9 w-44 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="button" class="h-9 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-800 hover:bg-yellow-500">
                Search
            </button>
        </div>

        
        <div class="mb-2 flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Date Filter</span>

            <select class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm">
                <option>Search Date</option>
            </select>

            <input type="date" value="2025-10-26"
                class="h-9 ml-3 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="text-xs text-gray-500">To</span>

            <input type="date" value="2025-10-26"
                class="h-9 rounded border border-gray-300 bg-white px-2 text-sm">
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Total Row</span>
            <input type="text" value="" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page Size</span>
            <input type="text" value="" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page No</span>
            <input type="text" value="" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="button" class="h-7 rounded bg-yellow-400 px-2 text-xs text-gray-800">Refresh</button>
            <button type="button" class="h-7 rounded bg-yellow-400 px-2 text-xs text-gray-800">Print</button>
        </div>
    </div>

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
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Create</button>
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
                    <th class="w-9 border border-yellow-300 px-2 py-2 text-center">
                        <input type="checkbox" class="h-3 w-3">
                    </th>
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
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                <tr>
                    <td colspan="12" class="border border-yellow-200 px-3 py-4 text-right text-gray-500">
                        No records to view
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div>
        <button type="button" class="rounded border border-yellow-500 bg-yellow-300 px-2 py-1 text-sm font-medium text-gray-800">Delete</button>
    </div>
</div>
@endsection
