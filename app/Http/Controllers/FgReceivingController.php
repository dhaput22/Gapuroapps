<?php

namespace App\Http\Controllers;

use App\Models\FgDeliveryScan;
use App\Models\FgReceivingScan;
use App\Models\FgSwaPlan;
use App\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FgReceivingController extends Controller
{
    private const UNREGISTERED_SCAN_IDS_SESSION_KEY = 'fg_receiving_unregistered_scan_ids';
    private const LOT_NO_INVALID_MESSAGE = 'Lot No. is wrong, does not match the FG plan for SWA.';

    public function index(Request $request): View
    {
        $today = now()->format('Y-m-d');
        [$sortBy, $sortDir] = $this->resolveSort(
            $request,
            ['scanned_at', 'part_code', 'part_name', 'lot_no', 'qty_box', 'created_at'],
            'scanned_at'
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
        $filteredQuery = $this->buildReceivingQuery($request);

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
        return view('fg-storage.receiving', compact('scans', 'summary', 'filters', 'operators'));
    }

    public function createUnregistered(Request $request): View
    {
        [$sortBy, $sortDir] = $this->resolveSort(
            $request,
            ['scanned_at', 'part_code', 'part_name', 'lot_no', 'qty_box', 'created_at'],
            'scanned_at'
        );

        $filters = [
            'receiving_date' => (string) $request->input('receiving_date', now()->format('Y-m-d')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ];
        $defaultPartCode = trim((string) $request->query('part_code', ''));
        $defaultOperatorEmployeeId = trim((string) $request->query('operator_employee_id', ''));

        if (!$request->boolean('carry')) {
            $request->session()->forget(self::UNREGISTERED_SCAN_IDS_SESSION_KEY);
        }

        $scanIds = collect((array) $request->session()->get(self::UNREGISTERED_SCAN_IDS_SESSION_KEY, []))
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $page = max(1, (int) $request->input('page', 1));
        $query = FgReceivingScan::query()
            ->with('operator')
            ->whereIn('id', $scanIds);
        $scans = (clone $query)
            ->orderBy($sortBy, $sortDir)
            ->when($sortBy !== 'id', fn($builder) => $builder->orderByDesc('id'))
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $totalCount = (clone $query)->count();

        return view('fg-storage.receiving-unregistered', compact('filters', 'scans', 'totalCount', 'defaultPartCode', 'defaultOperatorEmployeeId'));
    }

    public function edit(FgReceivingScan $scan): View
    {
        $operators = Operator::query()->orderBy('name')->get();
        return view('fg-storage.receiving-edit', compact('scan', 'operators'));
    }

    public function update(Request $request, FgReceivingScan $scan): RedirectResponse
    {
        $validated = $request->validate([
            'part_code' => ['required', 'string', 'max:100'],
            'part_name' => ['nullable', 'string', 'max:255'],
            'lot_no' => ['required', 'string', 'max:100'],
            'qty_box' => ['required', 'integer', 'min:0'],
            'scanned_at' => ['nullable', 'date'],
            'operator_id' => ['nullable', 'exists:operators,id'],
        ]);

        $scan->update([
            'part_code' => trim($validated['part_code']),
            'part_name' => isset($validated['part_name']) ? trim($validated['part_name']) : $scan->part_name,
            'lot_no' => trim($validated['lot_no']),
            'qty_box' => (int) $validated['qty_box'],
            'scanned_at' => $validated['scanned_at'] ?? $scan->scanned_at,
            'operator_id' => $validated['operator_id'] ?? $scan->operator_id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'FG Receiving data has been successfully updated.');
    }

    public function destroy(FgReceivingScan $scan): RedirectResponse
    {
        $scan->delete();

        return redirect()
            ->back()
            ->with('success', 'FG Receiving data has been successfully deleted.');
    }

    public function storeUnregistered(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'operator_employee_id' => ['required', 'string', 'max:50'],
            'part_code' => ['required', 'string', 'max:100'],
            'lot_no' => ['required', 'string', 'max:100'],
            'receiving_date' => ['nullable', 'date'],
        ]);

        $operatorEmployeeId = trim($validated['operator_employee_id']);
        $partCode = trim($validated['part_code']);
        $lotNo = trim($validated['lot_no']);
        $receivingDate = (string) ($validated['receiving_date'] ?? now()->format('Y-m-d'));

        $operator = Operator::query()->where('employee_id', $operatorEmployeeId)->first();
        if (!$operator) {
            return back()
                ->withErrors(['operator_employee_id' => 'Nomor ID operator tidak ditemukan.'])
                ->withInput();
        }

        $deliveryScan = $this->findDeliveryScanByPartAndLot($partCode, $lotNo);
        if ($deliveryScan !== null) {
            if ($this->isDuplicateLotScan($partCode, $lotNo)) {
                return back()
                    ->withErrors(['lot_no' => 'Lot No ini sudah ada di FG Receiving.'])
                    ->withInput();
            }

            $scan = FgReceivingScan::query()->create([
                'label_id' => $deliveryScan->label_id,
                'part_code' => $deliveryScan->part_code,
                'part_name' => $deliveryScan->part_name,
                'lot_no' => $deliveryScan->lot_no,
                'qty_box' => (int) $deliveryScan->qty_box,
                'scanned_at' => $this->isDateString($receivingDate) ? $receivingDate . ' ' . now()->format('H:i:s') : now(),
                'created_by' => $request->user()?->id,
                'operator_id' => $operator->id,
            ]);

            $deliveryScan->delete();

            $scanIds = collect((array) $request->session()->get(self::UNREGISTERED_SCAN_IDS_SESSION_KEY, []))
                ->map(fn($id) => (int) $id)
                ->filter(fn(int $id) => $id > 0)
                ->push((int) $scan->id)
                ->unique()
                ->values()
                ->all();

            $request->session()->put(self::UNREGISTERED_SCAN_IDS_SESSION_KEY, $scanIds);

            return redirect()
                ->route('fg.storage.receiving.create-unregistered', [
                    'receiving_date' => $receivingDate,
                    'carry' => 1,
                    'part_code' => $scan->part_code,
                    'operator_employee_id' => $operator->employee_id,
                ])
                ->with('success', 'Cancellation of shipment successful. Item returned to FG Receiving.');
        }

        [$selectedPlan, $errorMessage] = $this->resolvePlanForScan($partCode, $lotNo);

        if ($selectedPlan === null) {
            $errorField = $errorMessage === self::LOT_NO_INVALID_MESSAGE ? 'lot_no' : 'part_code';
            return back()
                ->withErrors([$errorField => $errorMessage ?? 'Plan for this part has already been fulfilled or the lot range has been exhausted.'])
                ->withInput();
        }

        if ($this->isDuplicateLotScan($partCode, $lotNo)) {
            return back()
                ->withErrors(['lot_no' => 'Lot No. has already been scanned for this part code.'])
                ->withInput();
        }

        $currentTotalScan = $this->calculateTotalScanForPlan($selectedPlan);
        if ($currentTotalScan + (int) $selectedPlan->qty_box > (int) $selectedPlan->total_plan) {
            return back()
                ->withErrors(['part_code' => 'Total scan plan has already reached the total plan limit.'])
                ->withInput();
        }

        $scan = FgReceivingScan::query()->create([
            'part_code' => $selectedPlan->part_code,
            'part_name' => $selectedPlan->part_name,
            'lot_no' => $lotNo,
            'qty_box' => (int) $selectedPlan->qty_box,
            'scanned_at' => $this->isDateString($receivingDate) ? $receivingDate . ' ' . now()->format('H:i:s') : now(),
            'created_by' => $request->user()?->id,
            'operator_id' => $operator->id,
        ]);

        $scanIds = collect((array) $request->session()->get(self::UNREGISTERED_SCAN_IDS_SESSION_KEY, []))
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->push((int) $scan->id)
            ->unique()
            ->values()
            ->all();

        $request->session()->put(self::UNREGISTERED_SCAN_IDS_SESSION_KEY, $scanIds);

        return redirect()
            ->route('fg.storage.receiving.create-unregistered', [
                'receiving_date' => $receivingDate,
                'carry' => 1,
                'part_code' => $selectedPlan->part_code,
                'operator_employee_id' => $operator->employee_id,
            ])
            ->with('success', 'Scan part has been successfully registered to FG Receiving.');
    }

    public function previewUnregisteredPlan(Request $request): JsonResponse
    {
        $partCode = trim((string) $request->query('part_code', ''));
        $lotNo = trim((string) $request->query('lot_no', ''));

        if ($partCode === '' || $lotNo === '') {
            return response()->json([
                'message' => 'Part Code and Lot No are required.',
            ], 422);
        }

        $deliveryScan = $this->findDeliveryScanByPartAndLot($partCode, $lotNo);
        if ($deliveryScan !== null) {
            return response()->json([
                'part_code' => $deliveryScan->part_code,
                'part_name' => $deliveryScan->part_name,
                'lot_no' => $deliveryScan->lot_no,
                'qty_box' => (int) $deliveryScan->qty_box,
                'message' => 'Lot is already in FG Delivery. Submit for cancellation of shipment and return to FG Receiving.',
                'action' => 'CANCEL_DELIVERY',
            ]);
        }

        [$plan, $errorMessage] = $this->resolvePlanForScan($partCode, $lotNo);
        if ($plan === null) {
            return response()->json([
                'message' => $errorMessage ?? 'Plan FG for SWA not found.',
            ], 422);
        }

        if ($this->isDuplicateLotScan($partCode, $lotNo)) {
            return response()->json([
                'message' => 'Lot No has already been scanned for this part code.',
            ], 422);
        }

        $currentTotalScan = $this->calculateTotalScanForPlan($plan);
        $nextTotalScan = $currentTotalScan + (int) $plan->qty_box;
        if ($nextTotalScan > (int) $plan->total_plan) {
            return response()->json([
                'message' => 'Total scan plan has already reached the total plan limit.',
            ], 422);
        }

        return response()->json([
            'part_code' => $plan->part_code,
            'part_name' => $plan->part_name,
            'lot_no' => $lotNo,
            'qty_box' => (int) $plan->qty_box,
            'total_scan_after_submit' => $nextTotalScan,
            'remaining_plan' => max(0, (int) $plan->total_plan - $nextTotalScan),
        ]);
    }

    public function previewUnregisteredPart(Request $request): JsonResponse
    {
        $partCode = trim((string) $request->query('part_code', ''));
        if ($partCode === '') {
            return response()->json([
                'message' => 'Part Code is required.',
            ], 422);
        }

        [$plan, $errorMessage] = $this->resolvePlanByPartCode($partCode);
        if ($plan !== null) {
            $currentTotalScan = $this->calculateTotalScanForPlan($plan);

            return response()->json([
                'part_code' => $plan->part_code,
                'part_name' => $plan->part_name,
                'qty_box' => (int) $plan->qty_box,
                'total_plan' => (int) $plan->total_plan,
                'total_scan' => $currentTotalScan,
                'remaining_plan' => max(0, (int) $plan->total_plan - $currentTotalScan),
            ]);
        }

        $deliveryScan = $this->findDeliveryScanByPartCode($partCode);
        if ($deliveryScan !== null) {
            return response()->json([
                'part_code' => $deliveryScan->part_code,
                'part_name' => $deliveryScan->part_name,
                'qty_box' => (int) $deliveryScan->qty_box,
                'message' => 'Part found in FG Delivery. Continue scanning Lot No for shipment cancellation.',
                'source' => 'delivery',
            ]);
        }

        return response()->json([
            'message' => $errorMessage ?? 'Part Code not found in the plan.',
        ], 422);
    }

    private function buildReceivingQuery(Request $request)
    {
        $query = FgReceivingScan::query()->with('operator');

        $today = now()->format('Y-m-d');
        $dateFrom = substr((string) $request->input('date_from', $today), 0, 10);
        $dateTo = substr((string) $request->input('date_to', $today), 0, 10);
        if ($this->isDateString($dateFrom) && $this->isDateString($dateTo)) {
            if ($dateFrom > $dateTo) {
                [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
            }

            $expr = $this->lotDateSqlExpression('lot_no');
            $query->whereRaw("$expr BETWEEN ? AND ?", [$dateFrom, $dateTo]);
        }

        $searchBy = (string) $request->input('search_by', '');
        $keyword = trim((string) $request->input('keyword', ''));
        $searchable = ['part_code', 'part_name', 'lot_no', 'label_id'];

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

    private function resolvePlanForScan(string $partCode, string $lotNo): array
    {
        $candidatePlans = $this->plansByPartCode($partCode);

        if ($candidatePlans->isEmpty()) {
            return [null, 'Part code is not yet registered in Plan FG for SWA.'];
        }

        foreach ($candidatePlans as $plan) {
            if ($this->isLotWithinRange($lotNo, $plan->start_lot_no, $plan->end_lot_no)) {
                return [$plan, null];
            }
        }

        return [null, self::LOT_NO_INVALID_MESSAGE];
    }

    private function resolvePlanByPartCode(string $partCode): array
    {
        $candidatePlans = $this->plansByPartCode($partCode);
        if ($candidatePlans->isEmpty()) {
            return [null, 'Part code is not yet registered in Plan FG for SWA.'];
        }

        foreach ($candidatePlans as $plan) {
            $currentTotalScan = $this->calculateTotalScanForPlan($plan);
            if ($currentTotalScan < (int) $plan->total_plan) {
                return [$plan, null];
            }
        }

        return [$candidatePlans->first(), null];
    }

    private function plansByPartCode(string $partCode): Collection
    {
        return FgSwaPlan::query()
            ->where('part_code', $partCode)
            ->orderByDesc('id')
            ->get();
    }

    private function isDuplicateLotScan(string $partCode, string $lotNo): bool
    {
        $normalizedLot = $this->normalizeLot($lotNo);
        if ($normalizedLot === '') {
            return false;
        }

        $existing = FgReceivingScan::query()
            ->where('part_code', $partCode)
            ->get(['lot_no']);

        foreach ($existing as $scan) {
            if ($this->normalizeLot((string) $scan->lot_no) === $normalizedLot) {
                return true;
            }
        }

        return false;
    }

    private function calculateTotalScanForPlan(FgSwaPlan $plan): int
    {
        return (int) $this->fetchPlanScans($plan)->sum('qty_box');
    }

    private function fetchPlanScans(FgSwaPlan $plan): Collection
    {
        return FgReceivingScan::query()
            ->where('part_code', $plan->part_code)
            ->get()
            ->filter(function (FgReceivingScan $scan) use ($plan) {
                return $this->isLotWithinRange((string) $scan->lot_no, $plan->start_lot_no, $plan->end_lot_no);
            })
            ->values();
    }

    private function isLotWithinRange(string $lot, string $start, string $end): bool
    {
        if ($this->compareLots($start, $end) > 0) {
            [$start, $end] = [$end, $start];
        }

        return $this->compareLots($lot, $start) >= 0
            && $this->compareLots($lot, $end) <= 0;
    }

    private function compareLots(string $left, string $right): int
    {
        $leftParsed = $this->parseLot($left);
        $rightParsed = $this->parseLot($right);

        if (
            $leftParsed !== null
            && $rightParsed !== null
            && $leftParsed['prefix'] === $rightParsed['prefix']
        ) {
            return $leftParsed['number'] <=> $rightParsed['number'];
        }

        return strcmp($this->normalizeLot($left), $this->normalizeLot($right));
    }

    private function parseLot(string $lot): ?array
    {
        $normalized = $this->normalizeLot($lot);
        if ($normalized === '') {
            return null;
        }

        if (!preg_match('/^(.*?)(\d+)$/', $normalized, $matches)) {
            return null;
        }

        return [
            'prefix' => $matches[1],
            'number' => (int) $matches[2],
            'length' => strlen($matches[2]),
        ];
    }

    private function normalizeLot(string $value): string
    {
        return strtoupper((string) preg_replace('/[^A-Z0-9\-]/i', '', trim($value)));
    }

    private function normalizeCode(string $value): string
    {
        return strtoupper((string) preg_replace('/[^A-Z0-9]/i', '', trim($value)));
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
}
