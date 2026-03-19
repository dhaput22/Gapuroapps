@extends('layouts.app')

@section('title', 'FG Receiving Unregistered')

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

    <div class="text-center">
        <h1 class="text-2xl font-semibold text-gray-700">FG Storage</h1>
        <h2 class="mt-1 text-[32px] font-bold leading-tight text-gray-800">FG Receiving Unregistered</h2>
    </div>

    <div class="grid gap-x-12 gap-y-3 px-1 lg:grid-cols-2 lg:px-10">
        <div class="space-y-3">
            <div class="flex items-center">
                <label class="w-40 font-medium">Receiving Date</label>
                <span class="mr-3">:</span>
                <input type="text" value="{{ now()->format('Y-m-d') }}"
                    class="h-8 w-44 rounded border border-gray-300 bg-gray-50 px-3 text-sm focus:border-gray-400 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Storage</label>
                <span class="mr-3">:</span>
                <select class="h-8 w-44 rounded border border-gray-300 bg-white px-2 text-xs focus:border-gray-400 focus:outline-none">
                    <option>2nd Floor</option>
                    <option>1st Floor</option>
                    <option>Warehouse</option>
                </select>
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Part Code</label>
                <span class="mr-3">:</span>
                <input type="text"
                    class="h-8 w-72 rounded border border-gray-300 bg-white px-3 text-sm focus:border-gray-400 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Part Name</label>
                <span class="mr-3">:</span>
                <input type="text"
                    class="h-8 w-72 rounded border border-gray-300 bg-white px-3 text-sm focus:border-gray-400 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Lot No</label>
                <span class="mr-3">:</span>
                <input type="text"
                    class="h-8 w-72 rounded border border-gray-300 bg-white px-3 text-sm focus:border-gray-400 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Qty</label>
                <span class="mr-3">:</span>
                <input type="text"
                    class="h-8 w-44 rounded border border-gray-300 bg-white px-3 text-sm focus:border-gray-400 focus:outline-none" />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center">
                <label class="w-32 font-medium">Total Count</label>
                <span class="mr-3">:</span>
                <div class="h-8 w-40 rounded border border-transparent px-3 leading-8 text-gray-600">0</div>
            </div>
            <div class="flex items-center">
                <label class="w-32 font-medium">Receive By</label>
                <span class="mr-3">:</span>
                <input type="text" value="2190888"
                    class="h-8 w-36 rounded border border-gray-300 bg-gray-50 px-3 text-sm focus:border-gray-400 focus:outline-none" />
                <span class="ml-3 text-sm font-medium">EDI SUKASNO</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Injection Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Part Code</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Part Name</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Lot No</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Qty</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Delivery</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Delivery Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">QC Status</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Remark</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                <tr>
                    <td colspan="11" class="border border-yellow-200 px-3 py-1 text-center text-gray-500">
                        Page <input type="text" value="1"
                            class="mx-1 h-5 w-8 rounded border border-gray-300 px-1 text-center text-xs"> of 0
                    </td>
                </tr>
                <tr>
                    <td colspan="11" class="border border-yellow-200 px-3 py-2 text-right text-gray-500">
                        No record to view
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
