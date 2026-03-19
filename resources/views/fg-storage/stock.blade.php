@extends('layouts.app')

@section('title', 'FG Stock')

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
                <option>Injection Date</option>
                <option>Search Date</option>
            </select>

            <input type="date" value="2025-10-25" class="h-9 rounded border border-gray-300 bg-white px-2 text-sm">
            <span class="text-xs text-gray-500">To</span>
            <input type="date" value="2025-10-26" class="h-9 rounded border border-gray-300 bg-white px-2 text-sm">

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

        <div class="flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Total Row</span>
            <input type="text" value="3" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page Size</span>
            <input type="text" value="20" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page No</span>
            <input type="text" value="1" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="button" class="h-7 rounded bg-yellow-400 px-2 text-xs text-gray-800">Refresh</button>
            <button type="button" class="h-7 rounded bg-yellow-400 px-2 text-xs text-gray-800">Print</button>
        </div>
    </div>

    <div class="flex flex-wrap gap-1 text-sm">
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Incoming</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Request</a>
        <a href="{{ route('fg.storage.receiving') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Receiving</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Return</a>
        <a href="{{ route('fg.storage') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Delivery</a>
        <a href="{{ route('fg.storage.stock') }}" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 font-semibold text-gray-800">FG Stock</a>
        <a href="{{ route('fg.storage.swa') }}" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG for SWA</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG OnHold</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">FG Dispose</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Customer Return</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Stock</a>
    </div>

    <div class="text-sm">
        <a href="#" class="inline-block rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Summary Delivery</a>
    </div>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="w-8 border border-yellow-300 px-2 py-2 text-center">#</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Injection Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Part Code</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Part Name</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Start Lot No</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">End Lot No</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Qty Box</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Total Scan</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Total O/P</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Status</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Remark</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">1</td>
                    <td class="border border-yellow-200 px-2 py-1">2025-10-25</td>
                    <td class="border border-yellow-200 px-2 py-1">171739801</td>
                    <td class="border border-yellow-200 px-2 py-1">BOTTLE,70,5300</td>
                    <td class="border border-yellow-200 px-2 py-1">BL23PA-25001 +</td>
                    <td class="border border-yellow-200 px-2 py-1">BL23PA-25087 +</td>
                    <td class="border border-yellow-200 px-2 py-1">165</td>
                    <td class="border border-yellow-200 px-2 py-1">14355</td>
                    <td class="border border-yellow-200 px-2 py-1">14355</td>
                    <td class="border border-yellow-200 px-2 py-1">2nd Floor</td>
                    <td class="border border-yellow-200 px-2 py-1">NOT FOUND</td>
                    <td class="border border-yellow-200 px-2 py-1">Direct Receiving</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">2</td>
                    <td class="border border-yellow-200 px-2 py-1">2025-10-25</td>
                    <td class="border border-yellow-200 px-2 py-1">171739801</td>
                    <td class="border border-yellow-200 px-2 py-1">BOTTLE,70,5300</td>
                    <td class="border border-yellow-200 px-2 py-1">BL24PA-25001 +</td>
                    <td class="border border-yellow-200 px-2 py-1">BL24PA-25083 +</td>
                    <td class="border border-yellow-200 px-2 py-1">165</td>
                    <td class="border border-yellow-200 px-2 py-1">8745</td>
                    <td class="border border-yellow-200 px-2 py-1">13695</td>
                    <td class="border border-yellow-200 px-2 py-1">2nd Floor</td>
                    <td class="border border-yellow-200 px-2 py-1">NOT FOUND</td>
                    <td class="border border-yellow-200 px-2 py-1">Direct Receiving</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">3</td>
                    <td class="border border-yellow-200 px-2 py-1">2025-10-25</td>
                    <td class="border border-yellow-200 px-2 py-1">174983900</td>
                    <td class="border border-yellow-200 px-2 py-1">HOUSING</td>
                    <td class="border border-yellow-200 px-2 py-1">H501PA-25001 +</td>
                    <td class="border border-yellow-200 px-2 py-1">H501PA-25024 +</td>
                    <td class="border border-yellow-200 px-2 py-1">216</td>
                    <td class="border border-yellow-200 px-2 py-1">5184</td>
                    <td class="border border-yellow-200 px-2 py-1">5184</td>
                    <td class="border border-yellow-200 px-2 py-1">2nd Floor</td>
                    <td class="border border-yellow-200 px-2 py-1">NOT FOUND</td>
                    <td class="border border-yellow-200 px-2 py-1">Direct Receiving</td>
                </tr>
                <tr>
                    <td colspan="12" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                        Page <input type="text" value="1" class="mx-1 h-5 w-8 rounded border border-gray-300 px-1 text-center text-xs"> of 1
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
