@extends('layouts.app')

@section('title', $mode === 'edit' ? 'Edit Plan FG for SWA' : 'Create Plan FG for SWA')

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

    <h1 class="text-center text-2xl font-semibold text-gray-700">FG for SWA Plan</h1>

    @if ($errors->any())
        <div class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="rounded border border-gray-200 bg-gray-100 px-4 py-4">
        @csrf
        @if ($httpMethod !== 'POST')
            @method($httpMethod)
        @endif

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Part Code</label>
                <input type="text" name="part_code" value="{{ old('part_code', $plan->part_code) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Part Name</label>
                <input type="text" name="part_name" value="{{ old('part_name', $plan->part_name) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Start Lot No</label>
                <input type="text" name="start_lot_no" value="{{ old('start_lot_no', $plan->start_lot_no) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">End Lot No</label>
                <input type="text" name="end_lot_no" value="{{ old('end_lot_no', $plan->end_lot_no) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Qty Box</label>
                <input type="number" min="1" name="qty_box" value="{{ old('qty_box', $plan->qty_box) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Total Plan</label>
                <input type="number" min="1" name="total_plan" value="{{ old('total_plan', $plan->total_plan) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <button type="submit" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 text-sm font-semibold text-gray-800">
                {{ $mode === 'edit' ? 'Update Plan' : 'Create/Register Plan' }}
            </button>
            <a href="{{ route('fg.storage.swa') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700">
                Back
            </a>
        </div>
    </form>
</div>
@endsection
