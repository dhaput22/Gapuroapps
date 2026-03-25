@extends('layouts.app')

@section('title', 'Operator')

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

    <h1 class="text-center text-2xl font-semibold text-gray-700">Master Operator</h1>

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

    <form method="POST" action="{{ route('operators.store') }}" class="rounded border border-gray-200 bg-gray-50 px-4 py-4">
        @csrf
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Nomor ID</label>
                <input type="text" name="employee_id" value="{{ old('employee_id') }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Departemen</label>
                <input type="text" name="department" value="{{ old('department') }}"
                    class="h-9 w-full rounded border border-gray-300 bg-white px-2 text-sm focus:border-gray-400 focus:outline-none">
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 text-sm font-semibold text-gray-800">
                Tambah Operator
            </button>
        </div>
    </form>

    <form method="GET" action="{{ route('operators.index') }}" class="rounded border border-gray-200 bg-gray-100 px-4 py-3">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-gray-600">Search</span>
            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="ID / Nama / Departemen"
                class="h-9 w-56 rounded border border-gray-300 bg-white px-2 text-sm">
            <span class="text-gray-600">Page Size</span>
            <input type="number" min="1" max="100" name="page_size" value="{{ $filters['page_size'] ?? 10 }}"
                class="h-9 w-20 rounded border border-gray-300 bg-white px-2 text-sm">
            <button type="submit" class="h-9 rounded bg-yellow-400 px-3 text-xs font-semibold text-gray-900 hover:bg-yellow-500">
                Search
            </button>
            <a href="{{ route('operators.index') }}" class="h-9 rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700">
                Reset
            </a>
        </div>
    </form>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="w-8 border border-yellow-300 px-2 py-2 text-center">#</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Nomor ID</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Nama</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Departemen</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                @forelse ($operators as $operator)
                    <tr>
                        <td class="border border-yellow-200 px-2 py-1 text-center">{{ ($operators->firstItem() ?? 0) + $loop->index }}</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ $operator->employee_id }}</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ $operator->name }}</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ $operator->department }}</td>
                        <td class="border border-yellow-200 px-2 py-1 text-center">
                            <form method="POST" action="{{ route('operators.destroy', $operator) }}" onsubmit="return confirm('Hapus operator ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded border border-red-300 bg-red-50 px-2 py-1 text-[11px] text-red-700">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border border-yellow-200 px-3 py-3 text-center text-gray-500">
                            Belum ada data operator.
                        </td>
                    </tr>
                @endforelse

                @if ($operators->count() > 0)
                    <tr>
                        <td colspan="5" class="border border-yellow-200 px-2 py-1 text-center text-gray-500">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ $operators->onFirstPage() ? '#' : $operators->url(1) }}"
                                    class="rounded border px-1 {{ $operators->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">
                                    &laquo;
                                </a>
                                <a href="{{ $operators->onFirstPage() ? '#' : $operators->previousPageUrl() }}"
                                    class="rounded border px-1 {{ $operators->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">
                                    &lsaquo;
                                </a>
                                <span>Page {{ $operators->currentPage() }} of {{ $operators->lastPage() }}</span>
                                <a href="{{ $operators->hasMorePages() ? $operators->nextPageUrl() : '#' }}"
                                    class="rounded border px-1 {{ $operators->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
                                    &rsaquo;
                                </a>
                                <a href="{{ $operators->hasMorePages() ? $operators->url($operators->lastPage()) : '#' }}"
                                    class="rounded border px-1 {{ $operators->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">
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

