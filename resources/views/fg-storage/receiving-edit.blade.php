@extends('layouts.app')

@section('title', 'Edit FG Receiving')

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

    <h1 class="text-center text-2xl font-semibold text-gray-700">Edit FG Receiving</h1>

    @if ($errors->any())
    <div class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
    @endif

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

    <form method="POST" action="{{ route('fg.storage.receiving.update', $scan) }}" class="rounded border border-gray-200 bg-gray-50 px-6 py-5 max-w-xl">
        @csrf
        @method('PUT')

        <div class="space-y-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Part Code</label>
                <input type="text" name="part_code" value="{{ old('part_code', $scan->part_code) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                @error('part_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Part Name</label>
                <input type="text" name="part_name" value="{{ old('part_name', $scan->part_name) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                @error('part_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Lot No</label>
                <input type="text" name="lot_no" value="{{ old('lot_no', $scan->lot_no) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                @error('lot_no')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Qty Box</label>
                <input type="number" name="qty_box" value="{{ old('qty_box', $scan->qty_box) }}" min="0"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                @error('qty_box')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Injection Date</label>
                <input type="date" name="scanned_at" value="{{ old('scanned_at', optional($scan->scanned_at)->format('Y-m-d')) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                @error('scanned_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Operator</label>
                <select name="operator_id" class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
                    <option value="">— Pilih Operator —</option>
                    @foreach ($operators as $operator)
                        <option value="{{ $operator->id }}" {{ old('operator_id', $scan->operator_id) == $operator->id ? 'selected' : '' }}>
                            {{ $operator->employee_id }} - {{ $operator->name }}
                        </option>
                    @endforeach
                </select>
                @error('operator_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-5 flex gap-2">
            <button type="submit" class="rounded border border-yellow-500 bg-yellow-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-yellow-400">
                Simpan
            </button>
            <a href="{{ route('fg.storage.receiving') }}" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
