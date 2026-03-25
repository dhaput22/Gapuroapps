<?php

namespace App\Http\Controllers;

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
    private const LOT_NO_INVALID_MESSAGE = 'Lot No salah, tidak sesuai dengan plan FG for SWA.';

    public function index(Request $request): View
    {
        $today = now()->format('Y-m-d');

        $filters = [
            'date_from' => (string) $request->input('date_from', $today),
            'date_to' => (string) $request->input('date_to', $today),
            'search_by' => (string) $request->input('search_by', ''),
            'keyword' => trim((string) $request->input('keyword', '')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
        ];

        $page = max(1, (int) $request->input('page', 1));
        $filteredQuery = $this->buildReceivingQuery($request);

        $scans = (clone $filteredQuery)
            ->latest('id')
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $summary = [
            'total_row' => $scans->total(),
            'total_qty' => (int) (clone $filteredQuery)->sum('qty_box'),
        ];

        return view('fg-storage.receiving', compact('scans', 'summary', 'filters'));
    }

    public function createUnregistered(Request $request): View
    {
        $filters = [
            'receiving_date' => (string) $request->input('receiving_date', now()->format('Y-m-d')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
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
            ->latest('id')
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $totalCount = count($scanIds);

        return view('fg-storage.receiving-unregistered', compact('filters', 'scans', 'totalCount', 'defaultPartCode', 'defaultOperatorEmployeeId'));
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

        [$selectedPlan, $errorMessage] = $this->resolvePlanForScan($partCode, $lotNo);

        if ($selectedPlan === null) {
            $errorField = $errorMessage === self::LOT_NO_INVALID_MESSAGE ? 'lot_no' : 'part_code';
            return back()
                ->withErrors([$errorField => $errorMessage ?? 'Plan untuk part ini sudah terpenuhi atau lot range sudah habis.'])
                ->withInput();
        }

        if ($this->isDuplicateLotScan($partCode, $lotNo)) {
            return back()
                ->withErrors(['lot_no' => 'Lot No ini sudah pernah discan untuk part code tersebut.'])
                ->withInput();
        }

        $currentTotalScan = $this->calculateTotalScanForPlan($selectedPlan);
        if ($currentTotalScan + (int) $selectedPlan->qty_box > (int) $selectedPlan->total_plan) {
            return back()
                ->withErrors(['part_code' => 'Total scan plan sudah mencapai batas total plan.'])
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
            ->with('success', 'Scan part berhasil diregister ke FG Receiving.');
    }

    public function previewUnregisteredPlan(Request $request): JsonResponse
    {
        $partCode = trim((string) $request->query('part_code', ''));
        $lotNo = trim((string) $request->query('lot_no', ''));

        if ($partCode === '' || $lotNo === '') {
            return response()->json([
                'message' => 'Part Code dan Lot No wajib diisi.',
            ], 422);
        }

        [$plan, $errorMessage] = $this->resolvePlanForScan($partCode, $lotNo);
        if ($plan === null) {
            return response()->json([
                'message' => $errorMessage ?? 'Plan FG for SWA tidak ditemukan.',
            ], 422);
        }

        if ($this->isDuplicateLotScan($partCode, $lotNo)) {
            return response()->json([
                'message' => 'Lot No ini sudah pernah discan.',
            ], 422);
        }

        $currentTotalScan = $this->calculateTotalScanForPlan($plan);
        $nextTotalScan = $currentTotalScan + (int) $plan->qty_box;
        if ($nextTotalScan > (int) $plan->total_plan) {
            return response()->json([
                'message' => 'Total scan plan sudah mencapai batas total plan.',
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
                'message' => 'Part Code wajib diisi.',
            ], 422);
        }

        [$plan, $errorMessage] = $this->resolvePlanByPartCode($partCode);
        if ($plan === null) {
            return response()->json([
                'message' => $errorMessage ?? 'Part Code belum terdaftar pada plan.',
            ], 422);
        }

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

    private function buildReceivingQuery(Request $request)
    {
        $query = FgReceivingScan::query()->with('operator');

        $today = now()->format('Y-m-d');
        $dateFrom = (string) $request->input('date_from', $today);
        $dateTo = (string) $request->input('date_to', $today);
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
            return [null, 'Part code belum terdaftar pada Plan FG for SWA.'];
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
            return [null, 'Part code belum terdaftar pada Plan FG for SWA.'];
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
