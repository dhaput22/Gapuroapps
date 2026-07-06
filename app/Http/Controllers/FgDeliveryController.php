<?php

namespace App\Http\Controllers;

use App\Models\FgDeliveryScan;
use App\Models\FgReceivingScan;
use App\Models\FgReturnScan;
use App\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FgDeliveryController extends Controller
{
    private const DELIVERY_SCAN_IDS_SESSION_KEY = 'fg_delivery_scan_ids';

    public function index(Request $request): View
    {
        $today = now()->format('Y-m-d');
        [$sortBy, $sortDir] = $this->resolveSort(
            $request,
            ['delivery_at', 'part_code', 'part_name', 'lot_no', 'qty_box', 'transfer_card_no', 'created_at'],
            'delivery_at'
        );

        $filters = [
            'date_from' => (string) $request->input('date_from', $today),
            'date_to' => (string) $request->input('date_to', $today),
            'search_by' => (string) $request->input('search_by', ''),
            'keyword' => trim((string) $request->input('keyword', '')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ];

        $page = max(1, (int) $request->input('page', 1));
        $filteredQuery = $this->buildDeliveryQuery($request);

        $scans = (clone $filteredQuery)
            ->orderBy($sortBy, $sortDir)
            ->when($sortBy !== 'id', fn($query) => $query->orderByDesc('id'))
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $summary = [
            'total_row' => $scans->total(),
            'total_qty' => (int) (clone $filteredQuery)->sum('qty_box'),
        ];

        $operators = Operator::query()->orderBy('name')->get();
        return view('fg-storage.delivery', compact('scans', 'summary', 'filters', 'operators'));
    }

    public function createScan(Request $request): View
    {
        [$sortBy, $sortDir] = $this->resolveSort(
            $request,
            ['delivery_at', 'part_code', 'part_name', 'lot_no', 'qty_box', 'created_at'],
            'delivery_at'
        );

        $filters = [
            'delivery_date' => (string) $request->input('delivery_date', now()->format('Y-m-d')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
            'transfer_card_no' => trim((string) $request->input('transfer_card_no', '')),
        ];
        $defaultPartCode = trim((string) $request->query('part_code', ''));
        $defaultOperatorEmployeeId = trim((string) $request->query('operator_employee_id', ''));

        if (!$request->boolean('carry')) {
            $request->session()->forget(self::DELIVERY_SCAN_IDS_SESSION_KEY);
        }

        $scanIds = collect((array) $request->session()->get(self::DELIVERY_SCAN_IDS_SESSION_KEY, []))
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $page = max(1, (int) $request->input('page', 1));
        $query = FgDeliveryScan::query()
            ->with(['operator', 'deliveryOperator'])
            ->whereIn('id', $scanIds);

        $scans = (clone $query)
            ->orderBy($sortBy, $sortDir)
            ->when($sortBy !== 'id', fn($builder) => $builder->orderByDesc('id'))
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $totalCount = (clone $query)->count();

        return view('fg-storage.index', compact('filters', 'scans', 'totalCount', 'defaultOperatorEmployeeId', 'defaultPartCode'));
    }

    public function storeScan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'operator_employee_id' => ['required', 'string', 'max:50'],
            'part_code' => ['required', 'string', 'max:100'],
            'lot_no' => ['required', 'string', 'max:100'],
            'delivery_date' => ['nullable', 'date'],
            'transfer_card_no' => ['nullable', 'string', 'max:100'],
        ]);

        $operatorEmployeeId = trim($validated['operator_employee_id']);
        $partCode = trim($validated['part_code']);
        $lotNo = trim($validated['lot_no']);
        $deliveryDate = (string) ($validated['delivery_date'] ?? now()->format('Y-m-d'));
        $transferCardNo = trim((string) ($validated['transfer_card_no'] ?? ''));

        $operator = Operator::query()->where('employee_id', $operatorEmployeeId)->first();
        if (!$operator) {
            return back()
                ->withErrors(['operator_employee_id' => 'Nomor ID operator tidak ditemukan.'])
                ->withInput();
        }

        $receivingScan = $this->findReceivingScanByPartAndLot($partCode, $lotNo);

        // Cek di FG Return jika tidak ada di FG Receiving
        if ($receivingScan === null) {
            $returnScan = $this->findReturnScanByPartAndLot($partCode, $lotNo);
            if ($returnScan !== null) {
                $deliveryScan = FgDeliveryScan::query()->create([
                    'label_id' => $returnScan->label_id,
                    'part_code' => $returnScan->part_code,
                    'part_name' => $returnScan->part_name,
                    'lot_no' => $returnScan->lot_no,
                    'qty_box' => (int) $returnScan->qty_box,
                    'scanned_at' => $returnScan->scanned_at,
                    'operator_id' => $returnScan->operator_id,
                    'created_by' => $returnScan->created_by,
                    'delivery_at' => $this->isDateString($deliveryDate) ? $deliveryDate . ' ' . now()->format('H:i:s') : now(),
                    'delivery_operator_id' => $operator->id,
                    'transfer_card_no' => $transferCardNo !== '' ? $transferCardNo : null,
                ]);

                $returnScan->delete();

                $sessionIds = collect((array) $request->session()->get(self::DELIVERY_SCAN_IDS_SESSION_KEY, []))
                    ->map(fn($id) => (int) $id)
                    ->filter(fn(int $id) => $id > 0)
                    ->push((int) $deliveryScan->id)
                    ->unique()
                    ->values()
                    ->all();

                $request->session()->put(self::DELIVERY_SCAN_IDS_SESSION_KEY, $sessionIds);

                return redirect()
                    ->route('fg.storage.delivery.scan', [
                        'delivery_date' => $deliveryDate,
                        'transfer_card_no' => $transferCardNo,
                        'carry' => 1,
                        'part_code' => $deliveryScan->part_code,
                        'operator_employee_id' => $operator->employee_id,
                    ])
                    ->with('success', 'Re-delivery from FG Return successful. Data moved to FG Delivery.');
            }

            $existsOnDelivery = $this->findDeliveryScanByPartAndLot($partCode, $lotNo);
            if ($existsOnDelivery !== null) {
                return back()
                    ->withErrors(['lot_no' => 'Item already exists in FG Delivery. If you want to cancel the shipment, please scan it again in FG Receiving.'])
                    ->withInput();
            }

            return back()
                ->withErrors(['lot_no' => 'The combination of Part Code and Lot No. has not been registered on FG Receiving or FG Return.'])
                ->withInput();
        }

        $deliveryScan = FgDeliveryScan::query()->create([
            'label_id' => $receivingScan->label_id,
            'part_code' => $receivingScan->part_code,
            'part_name' => $receivingScan->part_name,
            'lot_no' => $receivingScan->lot_no,
            'qty_box' => (int) $receivingScan->qty_box,
            'scanned_at' => $receivingScan->scanned_at,
            'operator_id' => $receivingScan->operator_id,
            'created_by' => $receivingScan->created_by,
            'delivery_at' => $this->isDateString($deliveryDate) ? $deliveryDate . ' ' . now()->format('H:i:s') : now(),
            'delivery_operator_id' => $operator->id,
            'transfer_card_no' => $transferCardNo !== '' ? $transferCardNo : null,
        ]);

        $receivingScan->delete();

        $sessionIds = collect((array) $request->session()->get(self::DELIVERY_SCAN_IDS_SESSION_KEY, []))
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->push((int) $deliveryScan->id)
            ->unique()
            ->values()
            ->all();

        $request->session()->put(self::DELIVERY_SCAN_IDS_SESSION_KEY, $sessionIds);

        return redirect()
            ->route('fg.storage.delivery.scan', [
                'delivery_date' => $deliveryDate,
                'transfer_card_no' => $transferCardNo,
                'carry' => 1,
                'part_code' => $deliveryScan->part_code,
                'operator_employee_id' => $operator->employee_id,
            ])
            ->with('success', 'Scan FG Delivery successful. Data moved to FG Delivery table.');
    }

    public function edit(FgDeliveryScan $scan): View
    {
        $operators = Operator::query()->orderBy('name')->get();
        return view('fg-storage.delivery-edit', compact('scan', 'operators'));
    }

    public function update(Request $request, FgDeliveryScan $scan): RedirectResponse
    {
        $validated = $request->validate([
            'part_code' => ['required', 'string', 'max:100'],
            'part_name' => ['nullable', 'string', 'max:255'],
            'lot_no' => ['required', 'string', 'max:100'],
            'qty_box' => ['required', 'integer', 'min:0'],
            'delivery_at' => ['nullable', 'date'],
            'delivery_operator_id' => ['nullable', 'exists:operators,id'],
            'transfer_card_no' => ['nullable', 'string', 'max:100'],
        ]);

        $scan->update([
            'part_code' => trim($validated['part_code']),
            'part_name' => isset($validated['part_name']) ? trim($validated['part_name']) : $scan->part_name,
            'lot_no' => trim($validated['lot_no']),
            'qty_box' => (int) $validated['qty_box'],
            'delivery_at' => $validated['delivery_at'] ?? $scan->delivery_at,
            'delivery_operator_id' => $validated['delivery_operator_id'] ?? $scan->delivery_operator_id,
            'transfer_card_no' => isset($validated['transfer_card_no']) ? (trim($validated['transfer_card_no']) !== '' ? trim($validated['transfer_card_no']) : null) : $scan->transfer_card_no,
        ]);

        return redirect()
            ->back()
            ->with('success', 'FG Delivery data has been successfully updated.');
    }

    public function destroy(FgDeliveryScan $scan): RedirectResponse
    {
        $scan->delete();

        return redirect()
            ->back()
            ->with('success', 'FG Delivery data has been successfully deleted.');
    }

    public function previewPart(Request $request): JsonResponse
    {
        $partCode = trim((string) $request->query('part_code', ''));
        if ($partCode === '') {
            return response()->json([
                'message' => 'Part Code is required.',
            ], 422);
        }

        $scan = $this->findReceivingScanByPartCode($partCode);
        if ($scan !== null) {
            return response()->json([
                'part_code' => $scan->part_code,
                'part_name' => $scan->part_name,
                'qty_box' => (int) $scan->qty_box,
                'message' => 'Part Code found. Continue scanning Lot No.',
            ]);
        }

        $returnScan = $this->findReturnScanByPartCode($partCode);
        if ($returnScan !== null) {
            return response()->json([
                'part_code' => $returnScan->part_code,
                'part_name' => $returnScan->part_name,
                'qty_box' => (int) $returnScan->qty_box,
                'source' => 'return',
                'message' => 'Part found in FG Return. Continue scanning Lot No for re-delivery.',
            ]);
        }

        $existsOnDelivery = $this->findDeliveryScanByPartCode($partCode);
        if ($existsOnDelivery !== null) {
            return response()->json([
                'message' => 'Part Code is already in FG Delivery. If you want to cancel the shipment, please scan it again in FG Receiving.',
            ], 422);
        }

        return response()->json([
            'message' => 'Part Code has not been registered on FG Receiving or FG Return.',
        ], 422);
    }

    public function previewScan(Request $request): JsonResponse
    {
        $partCode = trim((string) $request->query('part_code', ''));
        $lotNo = trim((string) $request->query('lot_no', ''));
        if ($partCode === '' || $lotNo === '') {
            return response()->json([
                'message' => 'Part Code and Lot No are required.',
            ], 422);
        }

        $scan = $this->findReceivingScanByPartAndLot($partCode, $lotNo);
        if ($scan !== null) {
            return response()->json([
                'part_code' => $scan->part_code,
                'part_name' => $scan->part_name,
                'lot_no' => $scan->lot_no,
                'qty_box' => (int) $scan->qty_box,
                'action' => 'DELIVERY',
                'message' => 'Lot found in FG Receiving. Submit to move to FG Delivery.',
            ]);
        }

        $returnScan = $this->findReturnScanByPartAndLot($partCode, $lotNo);
        if ($returnScan !== null) {
            return response()->json([
                'part_code' => $returnScan->part_code,
                'part_name' => $returnScan->part_name,
                'lot_no' => $returnScan->lot_no,
                'qty_box' => (int) $returnScan->qty_box,
                'action' => 'REDELIVER_FROM_RETURN',
                'message' => 'Lot found in FG Return. Submit for re-delivery.',
            ]);
        }

        $existsOnDelivery = $this->findDeliveryScanByPartAndLot($partCode, $lotNo);
        if ($existsOnDelivery !== null) {
            return response()->json([
                'message' => 'Item is already in FG Delivery. If you want to cancel the shipment, please scan it again in FG Receiving.',
            ], 422);
        }

        return response()->json([
            'message' => 'The combination of Part Code and Lot No. has not been registered on FG Receiving or FG Return.',
        ], 422);
    }

    private function buildDeliveryQuery(Request $request)
    {
        $query = FgDeliveryScan::query()
            ->with(['operator', 'deliveryOperator']);

        $today = now()->format('Y-m-d');
        $dateFrom = substr((string) $request->input('date_from', $today), 0, 10);
        $dateTo = substr((string) $request->input('date_to', $today), 0, 10);
        if ($this->isDateString($dateFrom) && $this->isDateString($dateTo)) {
            if ($dateFrom > $dateTo) {
                [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
            }

            $expr = "DATE(COALESCE(delivery_at, updated_at, created_at))";
            $query->whereRaw("$expr BETWEEN ? AND ?", [$dateFrom, $dateTo]);
        }

        $searchBy = (string) $request->input('search_by', '');
        $keyword = trim((string) $request->input('keyword', ''));
        $searchable = ['part_code', 'part_name', 'lot_no', 'transfer_card_no'];

        if ($keyword !== '') {
            if (in_array($searchBy, $searchable, true)) {
                $query->where($searchBy, 'like', '%' . $keyword . '%');
            } else {
                $query->where(function ($sub) use ($searchable, $keyword) {
                    foreach ($searchable as $index => $column) {
                        if ($index === 0) {
                            $sub->where($column, 'like', '%' . $keyword . '%');
                        } else {
                            $sub->orWhere($column, 'like', '%' . $keyword . '%');
                        }
                    }
                });
            }
        }

        return $query;
    }

    private function findReceivingScanByPartCode(string $partCode): ?FgReceivingScan
    {
        $normalizedPartCode = $this->normalizeCode($partCode);
        if ($normalizedPartCode === '') {
            return null;
        }

        return FgReceivingScan::query()
            ->orderByDesc('id')
            ->get()
            ->first(function (FgReceivingScan $scan) use ($normalizedPartCode) {
                return $this->normalizeCode((string) $scan->part_code) === $normalizedPartCode;
            });
    }

    private function findReceivingScanByPartAndLot(string $partCode, string $lotNo): ?FgReceivingScan
    {
        $normalizedPartCode = $this->normalizeCode($partCode);
        $normalizedLot = $this->normalizeLot($lotNo);
        if ($normalizedPartCode === '' || $normalizedLot === '') {
            return null;
        }

        return FgReceivingScan::query()
            ->orderByDesc('id')
            ->get()
            ->first(function (FgReceivingScan $scan) use ($normalizedPartCode, $normalizedLot) {
                return $this->normalizeCode((string) $scan->part_code) === $normalizedPartCode
                    && $this->normalizeLot((string) $scan->lot_no) === $normalizedLot;
            });
    }

    private function findReturnScanByPartCode(string $partCode): ?FgReturnScan
    {
        $normalizedPartCode = $this->normalizeCode($partCode);
        if ($normalizedPartCode === '') {
            return null;
        }

        return FgReturnScan::query()
            ->orderByDesc('id')
            ->get()
            ->first(function (FgReturnScan $scan) use ($normalizedPartCode) {
                return $this->normalizeCode((string) $scan->part_code) === $normalizedPartCode;
            });
    }

    private function findReturnScanByPartAndLot(string $partCode, string $lotNo): ?FgReturnScan
    {
        $normalizedPartCode = $this->normalizeCode($partCode);
        $normalizedLot = $this->normalizeLot($lotNo);
        if ($normalizedPartCode === '' || $normalizedLot === '') {
            return null;
        }

        return FgReturnScan::query()
            ->orderByDesc('id')
            ->get()
            ->first(function (FgReturnScan $scan) use ($normalizedPartCode, $normalizedLot) {
                return $this->normalizeCode((string) $scan->part_code) === $normalizedPartCode
                    && $this->normalizeLot((string) $scan->lot_no) === $normalizedLot;
            });
    }

    private function findDeliveryScanByPartCode(string $partCode): ?FgDeliveryScan
    {
        $normalizedPartCode = $this->normalizeCode($partCode);
        if ($normalizedPartCode === '') {
            return null;
        }

        return FgDeliveryScan::query()
            ->orderByDesc('id')
            ->get()
            ->first(function (FgDeliveryScan $scan) use ($normalizedPartCode) {
                return $this->normalizeCode((string) $scan->part_code) === $normalizedPartCode;
            });
    }

    private function findDeliveryScanByPartAndLot(string $partCode, string $lotNo): ?FgDeliveryScan
    {
        $normalizedPartCode = $this->normalizeCode($partCode);
        $normalizedLot = $this->normalizeLot($lotNo);
        if ($normalizedPartCode === '' || $normalizedLot === '') {
            return null;
        }

        return FgDeliveryScan::query()
            ->orderByDesc('id')
            ->get()
            ->first(function (FgDeliveryScan $scan) use ($normalizedPartCode, $normalizedLot) {
                return $this->normalizeCode((string) $scan->part_code) === $normalizedPartCode
                    && $this->normalizeLot((string) $scan->lot_no) === $normalizedLot;
            });
    }

    private function resolveSort(Request $request, array $sortableColumns, string $defaultColumn): array
    {
        $sortBy = (string) $request->input('sort_by', $defaultColumn);
        if (!in_array($sortBy, $sortableColumns, true)) {
            $sortBy = $defaultColumn;
        }

        $sortDir = strtolower((string) $request->input('sort_dir', 'desc'));
        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        return [$sortBy, $sortDir];
    }

    private function normalizeLot(string $value): string
    {
        return strtoupper((string) preg_replace('/[^A-Z0-9\-]/i', '', trim($value)));
    }

    private function normalizeCode(string $value): string
    {
        return strtoupper((string) preg_replace('/[^A-Z0-9]/i', '', trim($value)));
    }

    private function isDateString(string $date): bool
    {
        if ($date === '') {
            return false;
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    }

    private function lotDateSqlExpression(string $column): string
    {
        $prefix = "SUBSTRING_INDEX($column, '-', 1)";
        $yearCode = "UPPER(SUBSTRING($prefix, CHAR_LENGTH($prefix) - 1, 1))";
        $monthCode = "UPPER(SUBSTRING($prefix, CHAR_LENGTH($prefix), 1))";
        $dayCode = "CAST(SUBSTRING(REPLACE(REPLACE(SUBSTRING_INDEX($column, '-', -1), '+', ''), ' ', ''), 1, 2) AS UNSIGNED)";
        $yearNumber = "(2025 + (ASCII($yearCode) - ASCII('P')))";
        $monthNumber = "(CASE WHEN $monthCode REGEXP '^[1-9]$' THEN CAST($monthCode AS UNSIGNED) WHEN $monthCode = 'A' THEN 10 WHEN $monthCode = 'B' THEN 11 WHEN $monthCode = 'C' THEN 12 ELSE NULL END)";

        return "STR_TO_DATE(CONCAT($yearNumber, '-', LPAD($monthNumber, 2, '0'), '-', LPAD($dayCode, 2, '0')), '%Y-%m-%d')";
    }
}
