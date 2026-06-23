@extends('layouts.app')

@section('title', 'Edit Operator')

@section('content')
<div class="space-y-4 text-[13px] text-gray-700">
    <div class="flex items-center justify-between border border-gray-200 bg-gray-100 px-4 py-2">
        <p class="font-semibold">
            Current Period : <span class="font-medium">2025-10-01 - 2025-12-31</span>
        </p>
        <p class="text-sm">
            <span class="font-semibold">Master Data</span>
            <span class="mx-2 text-gray-400">|</span>
            Operator
        </p>
    </div>

    <h1 class="text-center text-2xl font-semibold text-gray-700">Edit Operator</h1>

    @if ($errors->any())
        <div class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('operators.update', $operator) }}" class="rounded border border-gray-200 bg-gray-50 px-4 py-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Nomor ID</label>
                <input type="text" name="employee_id" value="{{ old('employee_id', $operator->employee_id) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Nama</label>
                <input type="text" name="name" value="{{ old('name', $operator->name) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Departemen</label>
                <input type="text" name="department" value="{{ old('department', $operator->department) }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <button type="submit" class="inline-flex items-center gap-2 rounded border border-blue-500 bg-blue-300 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-blue-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Operator
            </button>
            <a href="{{ route('operators.index') }}" class="inline-flex items-center gap-2 rounded border border-gray-300 bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
