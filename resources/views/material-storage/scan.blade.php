@extends('layouts.app')

@section('title', 'Material Storage Scan')

@section('content')
<div class="space-y-4 text-[13px] text-gray-700">
    <div class="flex items-center justify-between border border-gray-200 bg-gray-100 px-4 py-2">
        <p class="font-semibold">
            Current Period : <span class="font-medium">2025-10-01 - 2025-12-31</span>
        </p>
        <p class="text-sm">
            <span class="font-semibold">Material Storage</span>
            <span class="mx-2 text-gray-400">|</span>
            Form No : <span class="font-semibold">268</span>
        </p>
    </div>

    <h1 class="text-center text-2xl font-semibold text-gray-700">Material Storage</h1>

    <div class="grid gap-x-12 gap-y-3 px-1 lg:grid-cols-2 lg:px-10">
        <div class="space-y-3">
            <div class="flex items-center">
                <label class="w-40 font-medium">Storage Date</label>
                <span class="mr-3">:</span>
                <input type="text" value="{{ now()->format('Y-m-d') }}"
                    class="h-8 w-44 rounded border border-gray-300 bg-gray-50 px-3 text-sm focus:border-gray-400 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Storage By</label>
                <span class="mr-3">:</span>
                <input type="text" value="2190888"
                    class="h-8 w-36 rounded border border-gray-300 bg-gray-50 px-3 text-sm focus:border-gray-400 focus:outline-none" />
                <span class="ml-3 text-sm font-medium">EDI SUKASNO</span>
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Lot ID</label>
                <span class="mr-3">:</span>
                <input type="text"
                    class="h-8 w-72 rounded border border-yellow-400 bg-yellow-50 px-3 text-sm focus:border-yellow-500 focus:outline-none" />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center">
                <label class="w-28 font-medium">Total Scan</label>
                <span class="mr-3">:</span>
                <div class="h-8 w-24 rounded border border-transparent px-3 leading-8 text-gray-700">0</div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Label ID</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage By</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                <tr>
                    <td colspan="3" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                        Page <input type="text" value="1" class="mx-1 h-5 w-8 rounded border border-gray-300 px-1 text-center text-xs"> of 0
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="border border-yellow-200 px-3 py-2 text-right text-gray-500">
                        No record to view
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
