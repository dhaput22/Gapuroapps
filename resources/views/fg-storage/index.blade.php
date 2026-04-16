@extends('layouts.app')

@section('title', 'FG Delivery Scan')

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
        <h2 class="mt-1 text-[32px] font-bold leading-tight text-gray-800">FG Delivery Scan</h2>
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
        $currentSortBy = $filters['sort_by'] ?? 'delivery_at';
        $currentSortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $sortUrl = static function (string $column) use ($filters, $currentSortBy, $currentSortDir, $defaultOperatorEmployeeId, $defaultPartCode): string {
            $nextDirection = $currentSortBy === $column && $currentSortDir === 'asc' ? 'desc' : 'asc';

            $query = [
                'delivery_date' => $filters['delivery_date'] ?? null,
                'transfer_card_no' => $filters['transfer_card_no'] ?? null,
                'page_size' => $filters['page_size'] ?? null,
                'carry' => 1,
                'part_code' => $defaultPartCode ?? null,
                'operator_employee_id' => $defaultOperatorEmployeeId ?? null,
                'sort_by' => $column,
                'sort_dir' => $nextDirection,
            ];

            $query = array_filter($query, static fn($value) => $value !== null && $value !== '');

            return route('fg.storage.delivery.scan', $query);
        };
    @endphp

    <form id="delivery-scan-form" method="POST" action="{{ route('fg.storage.delivery.scan.store') }}"
        class="grid gap-x-12 gap-y-3 rounded border border-gray-200 bg-gray-50 px-3 py-4 lg:grid-cols-2 lg:px-10">
        @csrf

        <div class="space-y-3">
            <div class="flex items-center">
                <label class="w-40 font-medium">Delivery Date</label>
                <span class="mr-3">:</span>
                <input type="date" name="delivery_date" value="{{ old('delivery_date', $filters['delivery_date']) }}"
                    class="h-8 w-44 rounded border border-gray-300 bg-white px-3 text-sm focus:border-gray-400 focus:outline-none" />
            </div>
            <div class="flex items-center">
                <label class="w-40 font-medium">Transfer Card No</label>
                <span class="mr-3">:</span>
                <input id="transfer_card_no_input" type="text" name="transfer_card_no" value="{{ old('transfer_card_no', $filters['transfer_card_no'] ?? '') }}"
                    class="h-8 w-72 rounded border border-gray-300 bg-white px-3 text-sm focus:border-gray-400 focus:outline-none" />
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
        </div>

        <div class="space-y-3">
            <div class="flex items-center">
                <label class="w-32 font-medium">Total Count</label>
                <span class="mr-3">:</span>
                <div class="h-8 w-40 rounded border border-transparent px-3 leading-8 text-gray-700">
                    {{ number_format($totalCount) }}
                </div>
            </div>
            <div class="flex items-center">
                <label class="w-32 font-medium">Delivery By</label>
                <span class="mr-3">:</span>
                <input id="operator_id_input" type="text" name="operator_employee_id"
                    value="{{ old('operator_employee_id', $defaultOperatorEmployeeId ?? '') }}"
                    class="h-8 w-36 rounded border border-gray-300 bg-white px-3 text-sm focus:border-yellow-500 focus:outline-none" />
                <span id="operator_name_preview" class="ml-3 text-sm font-medium text-gray-700"></span>
            </div>
            <p id="operator_department_preview" class="text-xs text-gray-500"></p>
            <p id="scan_hint" class="text-xs text-gray-500"></p>
            <div class="flex items-center gap-2">
                <button type="submit" class="rounded border border-yellow-500 bg-yellow-300 px-3 py-2 text-xs font-semibold text-gray-800">
                    Register (Enter)
                </button>
                <a href="{{ route('fg.storage') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700">
                    Back to FG Delivery
                </a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto rounded border border-yellow-300">
        <table class="min-w-full border-collapse text-xs">
            <thead class="bg-yellow-200 text-gray-700">
                <tr>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">
                        <a href="{{ $sortUrl('delivery_at') }}" class="hover:underline">Delivery Date</a>
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
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Transfer Card No</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Storage Date</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Delivery By</th>
                    <th class="whitespace-nowrap border border-yellow-300 px-2 py-2 text-left">Remark</th>
                </tr>
            </thead>
            <tbody class="bg-yellow-50">
                @forelse ($scans as $scan)
                    <tr>
                        <td class="border border-yellow-200 px-2 py-1">{{ optional($scan->delivery_at)->format('Y-m-d') ?? optional($scan->created_at)->format('Y-m-d') }}</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ $scan->part_code }}</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ $scan->part_name }}</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ $scan->lot_no }}</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ number_format((int) $scan->qty_box) }}</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ $scan->transfer_card_no ?: '-' }}</td>
                        <td class="border border-yellow-200 px-2 py-1">2nd Floor</td>
                        <td class="border border-yellow-200 px-2 py-1">{{ optional($scan->scanned_at)->format('Y-m-d') ?? optional($scan->created_at)->format('Y-m-d') }}</td>
                        @php($deliveryBy = $scan->deliveryOperator ?? $scan->operator)
                        <td class="border border-yellow-200 px-2 py-1">{{ $deliveryBy?->name ?? '-' }}</td>
                        <td class="border border-yellow-200 px-2 py-1">Delivery Process</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="border border-yellow-200 px-3 py-2 text-center text-gray-500">
                            No record to view
                        </td>
                    </tr>
                @endforelse

                @if ($scans->count() > 0)
                    <tr>
                        <td colspan="10" class="border border-yellow-200 px-3 py-1 text-center text-gray-500">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ $scans->onFirstPage() ? '#' : $scans->url(1) }}" class="rounded border px-1 {{ $scans->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">&laquo;</a>
                                <a href="{{ $scans->onFirstPage() ? '#' : $scans->previousPageUrl() }}" class="rounded border px-1 {{ $scans->onFirstPage() ? 'cursor-not-allowed bg-gray-100 text-gray-400' : 'bg-white text-gray-700' }}">&lsaquo;</a>
                                <span>Page {{ $scans->currentPage() }} of {{ $scans->lastPage() }}</span>
                                <a href="{{ $scans->hasMorePages() ? $scans->nextPageUrl() : '#' }}" class="rounded border px-1 {{ $scans->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">&rsaquo;</a>
                                <a href="{{ $scans->hasMorePages() ? $scans->url($scans->lastPage()) : '#' }}" class="rounded border px-1 {{ $scans->hasMorePages() ? 'bg-white text-gray-700' : 'cursor-not-allowed bg-gray-100 text-gray-400' }}">&raquo;</a>
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
        const form = document.getElementById('delivery-scan-form');
        const operatorInput = document.getElementById('operator_id_input');
        const operatorNamePreview = document.getElementById('operator_name_preview');
        const operatorDepartmentPreview = document.getElementById('operator_department_preview');
        const partCodeInput = document.getElementById('part_code_input');
        const lotInput = document.getElementById('lot_no_input');
        const partNamePreview = document.getElementById('part_name_preview');
        const qtyPreview = document.getElementById('qty_preview');
        const hint = document.getElementById('scan_hint');

        const operatorPreviewUrl = "{{ route('operators.preview') }}";
        const partPreviewUrl = "{{ route('fg.storage.delivery.scan.preview-part') }}";
        const lotPreviewUrl = "{{ route('fg.storage.delivery.scan.preview') }}";

        if (!form || !operatorInput || !operatorNamePreview || !operatorDepartmentPreview || !partCodeInput || !lotInput || !partNamePreview || !qtyPreview || !hint) return;

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

        const buildQueryUrl = (baseUrl, params) => {
            const query = new URLSearchParams(params).toString();
            const separator = baseUrl.includes('?') ? '&' : '?';
            return `${baseUrl}${separator}${query}`;
        };

        const previewOperator = async() => {
            if (operatorInput.value.trim() === '') {
                operatorInput.focus();
                return false;
            }

            const requestUrl = buildQueryUrl(operatorPreviewUrl, {
                employee_id: operatorInput.value.trim(),
            });

            try {
                const response = await fetch(requestUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
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
            } catch (error) {
                hint.textContent = 'Gagal membaca data operator. Coba lagi.';
                hint.className = 'text-xs text-red-600';
                operatorNamePreview.textContent = '';
                operatorDepartmentPreview.textContent = '';
                return false;
            }
        };

        const previewPart = async() => {
            if (partCodeInput.value.trim() === '') {
                partCodeInput.focus();
                return false;
            }

            const requestUrl = buildQueryUrl(partPreviewUrl, {
                part_code: partCodeInput.value.trim(),
            });

            try {
                const response = await fetch(requestUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const payload = await response.json();

                if (!response.ok) {
                    hint.textContent = payload.message || 'Part Code tidak valid untuk proses FG Delivery.';
                    hint.className = 'text-xs text-red-600';
                    partNamePreview.value = '';
                    qtyPreview.value = '';
                    return false;
                }

                partCodeInput.value = payload.part_code || partCodeInput.value.trim();
                partNamePreview.value = payload.part_name || '';
                qtyPreview.value = payload.qty_box ?? '';
                hint.textContent = payload.message || '';
                hint.className = 'text-xs text-green-700';
                return true;
            } catch (error) {
                hint.textContent = 'Gagal membaca data part. Coba lagi.';
                hint.className = 'text-xs text-red-600';
                partNamePreview.value = '';
                qtyPreview.value = '';
                return false;
            }
        };

        const previewLot = async() => {
            if (partCodeInput.value.trim() === '') {
                partCodeInput.focus();
                return false;
            }

            if (lotInput.value.trim() === '') {
                lotInput.focus();
                return false;
            }

            const requestUrl = buildQueryUrl(lotPreviewUrl, {
                part_code: partCodeInput.value.trim(),
                lot_no: lotInput.value.trim(),
            });

            try {
                const response = await fetch(requestUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const payload = await response.json();

                if (!response.ok) {
                    hint.textContent = payload.message || 'Lot No tidak valid untuk proses FG Delivery.';
                    hint.className = 'text-xs text-red-600';
                    return false;
                }

                partNamePreview.value = payload.part_name || '';
                qtyPreview.value = payload.qty_box ?? '';

                hint.textContent = payload.message || '';
                hint.className = payload.action === 'CANCEL' ? 'text-xs text-orange-700' : 'text-xs text-green-700';
                return true;
            } catch (error) {
                hint.textContent = 'Gagal membaca data lot. Coba lagi.';
                hint.className = 'text-xs text-red-600';
                return false;
            }
        };

        operatorInput.addEventListener('input', resetOperatorPreview);
        operatorInput.addEventListener('blur', () => {
            if (operatorInput.value.trim() !== '') {
                previewOperator();
            }
        });
        operatorInput.addEventListener('keydown', async(event) => {
            if (event.key !== 'Enter') return;
            event.preventDefault();

            const valid = await previewOperator();
            if (!valid) return;
            partCodeInput.focus();
        });

        partCodeInput.addEventListener('input', resetPartPreview);
        partCodeInput.addEventListener('blur', () => {
            if (partCodeInput.value.trim() !== '') {
                previewPart();
            }
        });
        partCodeInput.addEventListener('keydown', async(event) => {
            if (event.key !== 'Enter') return;
            event.preventDefault();

            const operatorValid = await previewOperator();
            if (!operatorValid) return;

            const partValid = await previewPart();
            if (!partValid) return;

            lotInput.focus();
        });

        lotInput.addEventListener('input', clearHint);
        lotInput.addEventListener('blur', () => {
            if (partCodeInput.value.trim() !== '' && lotInput.value.trim() !== '') {
                previewLot();
            }
        });

        lotInput.addEventListener('keydown', async(event) => {
            if (event.key !== 'Enter') return;
            event.preventDefault();

            const operatorValid = await previewOperator();
            if (!operatorValid) return;

            const partValid = await previewPart();
            if (!partValid) return;

            const lotValid = await previewLot();
            if (!lotValid) return;

            form.submit();
        });

        const hasOperator = operatorInput.value.trim() !== '';
        const hasPartCode = partCodeInput.value.trim() !== '';

        if (hasOperator) {
            previewOperator().then((ok) => {
                if (ok) {
                    if (hasPartCode) {
                        previewPart().then((partOk) => {
                            if (partOk) {
                                lotInput.focus();
                            } else {
                                partCodeInput.focus();
                            }
                        });
                    } else {
                        partCodeInput.focus();
                    }
                }
            });
        } else {
            operatorInput.focus();
        }
    })();
</script>
@endsection
