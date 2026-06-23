@extends('layouts.app')

@section('title', 'FG Return')

@section('content')
<div class="space-y-4 text-[13px] text-gray-700">
    <div class="flex items-center justify-between border border-gray-200 bg-gray-100 px-4 py-2">
        <p class="font-semibold">
            Current Period : <span class="font-medium">2025-10-01 - 2025-12-31</span>
        </p>
        <p class="text-sm">
            <span class="font-semibold">FG Storage</span>
            <span class="mx-2 text-gray-400">|</span>
            FG Return
        </p>
    </div>

    <div class="text-center">
        <h1 class="text-2xl font-semibold text-gray-700">FG Storage</h1>
        <h2 class="mt-1 text-[32px] font-bold leading-tight text-gray-800">FG Return</h2>
    </div>

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

    @php
    $currentSortBy = $filters['sort_by'] ?? 'return_at';
    $currentSortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

    $sortUrl = static function (string $column) use (
        $filters,
        $currentSortBy,
        $currentSortDir,
        $defaultPartCode,
        $defaultOperatorEmployeeId
    ): string {
        $nextDirection = $currentSortBy === $column && $currentSortDir === 'asc' ? 'desc' : 'asc';

        $query = [
            'return_date' => $filters['return_date'] ?? null,
            'page_size' => $filters['page_size'] ?? null,
            'carry' => 1,
            'part_code' => $defaultPartCode ?? null,
            'operator_employee_id' => $defaultOperatorEmployeeId ?? null,
            'sort_by' => $column,
            'sort_dir' => $nextDirection,
        ];

        $query = array_filter($query, static fn($value) => $value !== null && $value !== '');

        return route('fg.storage.return.create', $query);
    };
    @endphp

    <form id="return-form" method="POST" action="{{ route('fg.storage.return.store') }}"
        class="grid gap-x-12 gap-y-3 rounded border border-gray-200 bg-gray-50 px-3 py-4 lg:grid-cols-2 lg:px-10">
        @csrf

        <div class="space-y-3">
            <div class="flex items-center">
                <label class="w-40 font-medium">Return Date</label>
                <span class="mr-3">:</span>
                <input type="date" name="return_date" value="{{ old('return_date', $filters['return_date']) }}"
                    class="h-8 w-44 rounded border border-gray-300 bg-white px-3 text-sm focus:border-gray-400 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Part Code</label>
                <span class="mr-3">:</span>
                <input id="part_code_input" type="text" name="part_code" value="{{ old('part_code', $defaultPartCode ?? '') }}" autocomplete="off"
                    class="h-8 w-72 rounded border border-gray-300 bg-white px-3 text-sm focus:border-yellow-500 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Part Name</label>
                <span class="mr-3">:</span>
                <input id="part_name_preview" type="text" readonly value=""
                    class="h-8 w-72 rounded border border-gray-300 bg-gray-100 px-3 text-sm text-gray-600 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Lot No</label>
                <span class="mr-3">:</span>
                <input id="lot_no_input" type="text" name="lot_no" value="{{ old('lot_no') }}" autocomplete="off"
                    class="h-8 w-72 rounded border border-gray-300 bg-white px-3 text-sm focus:border-yellow-500 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Qty</label>
                <span class="mr-3">:</span>
                <input id="qty_preview" type="text" readonly value=""
                    class="h-8 w-44 rounded border border-gray-300 bg-gray-100 px-3 text-sm text-gray-600 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Remark</label>
                <span class="mr-3">:</span>
                <input id="remark_input" type="text" name="remark" value="{{ old('remark') }}" autocomplete="off"
                    class="h-8 w-72 rounded border border-gray-300 bg-white px-3 text-sm focus:border-yellow-500 focus:outline-none" />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center">
                <label class="w-40 font-medium">Total Count</label>
                <span class="mr-3">:</span>
                <div class="h-8 w-40 rounded border border-transparent px-3 leading-8 text-gray-700">
                    {{ number_format($totalCount) }}
                </div>
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Return By</label>
                <span class="mr-3">:</span>
                <input id="operator_id_input" type="text" name="operator_employee_id"
                    value="{{ old('operator_employee_id', $defaultOperatorEmployeeId ?? '') }}"
                    class="h-8 w-36 rounded border border-gray-300 bg-white px-3 text-sm focus:border-yellow-500 focus:outline-none" />
                <span id="operator_name_preview" class="ml-3 text-sm font-medium text-gray-700"></span>
            </div>
            <p id="operator_department_preview" class="text-xs text-gray-500"></p>
            <p id="scan_hint" class="text-xs text-gray-500"></p>
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded border border-yellow-500 bg-yellow-300 px-3 py-2 text-xs font-semibold text-gray-800 hover:bg-yellow-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Register (Enter)
                </button>
                <a href="{{ route('fg.storage.return.index') }}"
                    class="inline-flex items-center gap-1.5 rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Lihat Semua FG Return
                </a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('return_at') }}" class="hover:underline">Return Date</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('part_code') }}" class="hover:underline">Part Code</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('part_name') }}" class="hover:underline">Part Name</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('lot_no') }}" class="hover:underline">Lot No</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('qty_box') }}" class="hover:underline">Qty</a>
                    </th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Return By</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Remark</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                @forelse ($scans as $scan)
                <tr>
                    <td class="border border-yellow-200 px-2 py-1">{{ optional($scan->return_at)->format('Y-m-d') ?? optional($scan->created_at)->format('Y-m-d') }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->part_code }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->part_name ?? '-' }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->lot_no }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->qty_box > 0 ? number_format($scan->qty_box) : '-' }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->returnOperator?->name ?? '-' }}</td>
                    <td class="border border-yellow-200 px-2 py-1">{{ $scan->remark ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="border border-yellow-200 px-3 py-2 text-center text-gray-500">
                        No record to view
                    </td>
                </tr>
                @endforelse

                @if ($scans->count() > 0)
                <tr>
                    <td colspan="7" class="border border-yellow-200 px-3 py-1 text-center text-gray-500">
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
</div>

<script>
    (function() {
        const form = document.getElementById('return-form');
        const operatorInput = document.getElementById('operator_id_input');
        const operatorNamePreview = document.getElementById('operator_name_preview');
        const operatorDepartmentPreview = document.getElementById('operator_department_preview');
        const partCode = document.getElementById('part_code_input');
        const partNamePreview = document.getElementById('part_name_preview');
        const lotInput = document.getElementById('lot_no_input');
        const qtyPreview = document.getElementById('qty_preview');
        const hint = document.getElementById('scan_hint');
        const operatorPreviewUrl = "{{ route('operators.preview') }}";
        const partPreviewUrl = "{{ route('fg.storage.return.preview-part') }}";
        const scanPreviewUrl = "{{ route('fg.storage.return.preview') }}";

        if (!form || !operatorInput || !partCode || !lotInput) return;

        const clearHint = () => {
            hint.textContent = '';
            hint.className = 'text-xs text-gray-500';
        };

        const resetOperatorPreview = () => {
            operatorNamePreview.textContent = '';
            operatorDepartmentPreview.textContent = '';
            clearHint();
        };

        const resetPartPreview = () => {
            partNamePreview.value = '';
            qtyPreview.value = '';
            clearHint();
        };

        const previewOperator = async () => {
            if (operatorInput.value.trim() === '') {
                operatorInput.focus();
                return false;
            }

            const url = new URL(operatorPreviewUrl, window.location.origin);
            url.searchParams.set('employee_id', operatorInput.value.trim());

            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const payload = await response.json();

                if (!response.ok) {
                    hint.textContent = payload.message || 'Nomor ID operator tidak ditemukan.';
                    hint.className = 'text-xs text-red-600';
                    operatorNamePreview.textContent = '';
                    operatorDepartmentPreview.textContent = '';
                    return false;
                }

                operatorNamePreview.textContent = payload.name || '';
                operatorDepartmentPreview.textContent = payload.department ? `Departemen: ${payload.department}` : '';
                return true;
            } catch {
                hint.textContent = 'Gagal membaca data operator. Coba lagi.';
                hint.className = 'text-xs text-red-600';
                operatorNamePreview.textContent = '';
                operatorDepartmentPreview.textContent = '';
                return false;
            }
        };

        const previewPartCode = async () => {
            if (partCode.value.trim() === '') {
                partCode.focus();
                return false;
            }

            const url = new URL(partPreviewUrl, window.location.origin);
            url.searchParams.set('part_code', partCode.value.trim());

            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const payload = await response.json();

                if (!response.ok) {
                    hint.textContent = payload.message || 'Part Code tidak ditemukan di FG Delivery.';
                    hint.className = 'text-xs text-red-600';
                    partNamePreview.value = '';
                    qtyPreview.value = '';
                    return false;
                }

                partNamePreview.value = payload.part_name || '';
                qtyPreview.value = payload.qty_box ?? '';
                hint.textContent = `Part ditemukan di FG Delivery. Part Name: ${payload.part_name}.`;
                hint.className = 'text-xs text-green-700';
                return true;
            } catch {
                hint.textContent = 'Gagal membaca data. Coba lagi.';
                hint.className = 'text-xs text-red-600';
                partNamePreview.value = '';
                qtyPreview.value = '';
                return false;
            }
        };

        const previewScan = async () => {
            if (partCode.value.trim() === '') {
                partCode.focus();
                return false;
            }
            if (lotInput.value.trim() === '') {
                lotInput.focus();
                return false;
            }

            const url = new URL(scanPreviewUrl, window.location.origin);
            url.searchParams.set('part_code', partCode.value.trim());
            url.searchParams.set('lot_no', lotInput.value.trim());

            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const payload = await response.json();

                if (!response.ok) {
                    hint.textContent = payload.message || 'Data tidak ditemukan di FG Delivery.';
                    hint.className = 'text-xs text-red-600';
                    return false;
                }

                partNamePreview.value = payload.part_name || '';
                qtyPreview.value = payload.qty_box ?? '';
                hint.textContent = `Lot ditemukan di FG Delivery. Part: ${payload.part_name}, Qty: ${payload.qty_box}. Siap di-return.`;
                hint.className = 'text-xs text-green-700';
                return true;
            } catch {
                hint.textContent = 'Gagal membaca data. Coba lagi.';
                hint.className = 'text-xs text-red-600';
                return false;
            }
        };

        operatorInput.addEventListener('input', resetOperatorPreview);
        operatorInput.addEventListener('blur', () => {
            if (operatorInput.value.trim() !== '') previewOperator();
        });
        operatorInput.addEventListener('keydown', async (event) => {
            if (event.key !== 'Enter') return;
            event.preventDefault();
            const valid = await previewOperator();
            if (valid) partCode.focus();
        });

        partCode.addEventListener('input', resetPartPreview);
        partCode.addEventListener('blur', () => {
            if (partCode.value.trim() !== '') previewPartCode();
        });
        partCode.addEventListener('keydown', async (event) => {
            if (event.key !== 'Enter') return;
            event.preventDefault();
            if (partCode.value.trim() === '') return;
            const valid = await previewPartCode();
            if (valid) lotInput.focus();
        });

        lotInput.addEventListener('input', clearHint);
        lotInput.addEventListener('blur', () => {
            if (partCode.value.trim() !== '' && lotInput.value.trim() !== '') previewScan();
        });
        lotInput.addEventListener('keydown', async (event) => {
            if (event.key !== 'Enter') return;
            event.preventDefault();

            const operatorValid = await previewOperator();
            if (!operatorValid) return;

            const valid = await previewScan();
            if (!valid) return;

            form.submit();
        });

        const hasOperator = operatorInput.value.trim() !== '';
        const hasPartCode = partCode.value.trim() !== '';

        if (hasOperator) {
            previewOperator().then((operatorValid) => {
                if (!operatorValid) {
                    operatorInput.focus();
                    return;
                }
                if (hasPartCode) {
                    previewPartCode().then((partValid) => {
                        if (partValid) {
                            lotInput.focus();
                        } else {
                            partCode.focus();
                        }
                    });
                } else {
                    partCode.focus();
                }
            });
        } else {
            operatorInput.focus();
        }
    })();
</script>
@endsection
