@extends('layouts.app')

@section('title', 'Material Storage')

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

    <div class="rounded border border-gray-200 bg-gray-100 px-4 py-3">
        <div class="mb-2 flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Date Filter</span>

            <select class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm">
                <option>Filter Date</option>
            </select>

            <input type="date" value="{{ now()->format('Y-m-d') }}" class="h-9 w-36 rounded border border-gray-300 bg-white px-2 text-sm">
            <span class="text-xs text-gray-500">To</span>
            <input type="date" value="{{ now()->format('Y-m-d') }}" class="h-9 w-36 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-4 text-gray-600">Search By</span>

            <select class="h-9 rounded border border-yellow-400 bg-yellow-300 px-2 pr-8 text-sm">
                <option>Search By</option>
                <option>Part Code</option>
                <option>Label ID</option>
            </select>

            <input type="text" placeholder="Input Keyword" class="h-9 w-36 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="button" class="h-9 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-800 hover:bg-yellow-500">
                Search
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="w-20 text-gray-600">Total Row</span>
            <input type="text" value="10" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page Size</span>
            <input type="text" value="50" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <span class="ml-3 text-gray-600">Page No</span>
            <input type="text" value="1" class="h-7 w-12 rounded border border-gray-300 bg-white px-2 text-sm">

            <button type="button" class="h-7 rounded bg-yellow-400 px-2 text-xs text-gray-800">Refresh</button>
            <button type="button" class="h-7 rounded bg-yellow-400 px-2 text-xs text-gray-800">Print</button>
        </div>
    </div>

    <div class="flex flex-wrap gap-1 text-sm">
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">Incoming</a>
        <a href="#" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 font-semibold text-gray-800">Storage</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">TopUp</a>
        <a href="#" class="rounded border bg-white px-3 py-2 font-semibold text-blue-800 hover:bg-gray-100">History</a>
    </div>

    <div>
        <a href="{{ route('material.storage.scan') }}"
            class="inline-block rounded border border-yellow-500 bg-yellow-300 px-3 py-1 text-sm font-medium text-gray-800">
            Create
        </a>
    </div>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="w-8 border border-yellow-300 px-2 py-2 text-center">#</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Label ID</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Part Code</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Part Name</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">QTY</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">DO / No</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">LotNo</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">AreaName</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Status DO</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage By</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Last Update</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">1</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+24+PAC6570450</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">24</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0042025</td>
                    <td class="border border-yellow-200 px-2 py-1">PAC657</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">SEPTRIANA KURNIATY</td>
                    <td class="border border-yellow-200 px-2 py-1">2019-05-21</td>
                    <td class="border border-yellow-200 px-2 py-1">2019-05-21 17:03</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">2</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740084</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-23</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-23 17:03</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">3</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740098</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28 15:02</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">4</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740099</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28 15:02</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">5</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740100</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28 15:02</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">6</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740101</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28 15:02</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">7</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740105</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28 15:02</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">8</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740106</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28 15:02</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">9</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740110</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28 15:02</td>
                </tr>
                <tr>
                    <td class="border border-yellow-200 px-2 py-1 text-center">10</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900+25+ABD4740112</td>
                    <td class="border border-yellow-200 px-2 py-1">602014900</td>
                    <td class="border border-yellow-200 px-2 py-1">PP.AZ564</td>
                    <td class="border border-yellow-200 px-2 py-1">25</td>
                    <td class="border border-yellow-200 px-2 py-1">SSG0025276</td>
                    <td class="border border-yellow-200 px-2 py-1">ABD474</td>
                    <td class="border border-yellow-200 px-2 py-1">Pallet</td>
                    <td class="border border-yellow-200 px-2 py-1">OK</td>
                    <td class="border border-yellow-200 px-2 py-1">MELI</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28</td>
                    <td class="border border-yellow-200 px-2 py-1">2018-02-28 15:02</td>
                </tr>
                <tr>
                    <td colspan="12" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                        Page <input type="text" value="1" class="mx-1 h-5 w-8 rounded border border-gray-300 px-1 text-center text-xs"> of 50
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

