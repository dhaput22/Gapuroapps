<?php

namespace App\Http\Controllers;

use App\Models\FgDeliveryScan;
use App\Models\FgReceivingScan;
use App\Models\FgSwaPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FgSwaPlanController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->format('Y-m-d');
        [$sortBy, $sortDir] = $this->resolveSort($request);

        $filters = [
            'date_filter' => (string) $request->input('date_filter', 'created_at'),
            'date_from' => (string) $request->input('date_from', $today),
            'date_to' => (string) $request->input('date_to', $today),
            'search_by' => (string) $request->input('search_by', ''),
            'keyword' => trim((string) $request->input('keyword', '')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ];

        $page = max(1, (int) $request->input('page', $request->input('page_no', 1)));
        $filteredQuery = $this->buildFilteredPlanQuery($request);

        $plans = (clone $filteredQuery)
            ->orderBy($sortBy, $sortDir)
            ->when($sortBy !== 'id', fn($query) => $query->orderByDesc('id'))
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $plans->setCollection($this->attachTotalScan($plans->getCollection()));

        $allFilteredPlans = (clone $filteredQuery)->get();
        $allFilteredPlans = $this->attachTotalScan($allFilteredPlans);

        $summary = [
            'plan_count' => $plans->total(),
            'total_plan_registered' => (int) $allFilteredPlans->sum('total_plan'),
            'total_scan_registered' => (int) $allFilteredPlans->sum('total_scan'),
        ];

        return view('fg-storage.swa', compact('plans', 'summary', 'filters'));
    }

    public function create(): View
    {
        return view('fg-storage.swa-form', [
            'plan' => new FgSwaPlan(),
            'mode' => 'create',
            'formAction' => route('fg.storage.swa.store'),
            'httpMethod' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePlanPayload($request);

        FgSwaPlan::query()->create([
            ...$validated,
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('fg.storage.swa')
            ->with('success', 'Plan FG for SWA berhasil ditambahkan.');
    }

    public function edit(Request $request, FgSwaPlan $plan): View
    {
        $this->ensureWarehouseMutationPermission($request);

        return view('fg-storage.swa-form', [
            'plan' => $plan,
            'mode' => 'edit',
            'formAction' => route('fg.storage.swa.update', $plan),
            'httpMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, FgSwaPlan $plan): RedirectResponse
    {
        $this->ensureWarehouseMutationPermission($request);

        $validated = $this->validatePlanPayload($request, $plan->id);

        $plan->update($validated);

        return redirect()
            ->back()
            ->with('success', 'The plan has been successfully updated.');
    }

    public function destroy(Request $request, FgSwaPlan $plan): RedirectResponse
    {
        $this->ensureWarehouseMutationPermission($request);

        $plan->delete();

        return redirect()
            ->back()
            ->with('success', 'Plan FG for SWA successfully deleted.');
    }

    private function validatePlanPayload(Request $request, ?int $exceptId = null): array
    {
        $validator = Validator::make($request->all(), [
            'part_code' => ['required', 'string', 'max:100'],
            'part_name' => ['required', 'string', 'max:200'],
            'start_lot_no' => ['required', 'string', 'max:100'],
            'end_lot_no' => ['required', 'string', 'max:100'],
            'qty_box' => ['required', 'integer', 'min:1'],
            'total_plan' => ['required', 'integer', 'min:1'],
        ]);

        $validator->after(function ($validator) use ($request, $exceptId) {
            $partCode = (string) $request->input('part_code');
            $startLot = (string) $request->input('start_lot_no');
            $endLot = (string) $request->input('end_lot_no');

            if ($partCode === '' || $startLot === '' || $endLot === '') {
                return;
            }

            if ($this->compareLots($startLot, $endLot) > 0) {
                $validator->errors()->add('end_lot_no', 'End Lot No. must be greater than or equal to Start Lot No.');
                return;
            }

            $query = FgSwaPlan::query()->where('part_code', $partCode);
            if ($exceptId !== null) {
                $query->where('id', '!=', $exceptId);
            }

            $ranges = $query->get(['start_lot_no', 'end_lot_no']);
            foreach ($ranges as $range) {
                if ($this->rangesOverlap($startLot, $endLot, $range->start_lot_no, $range->end_lot_no)) {
                    $validator->errors()->add('start_lot_no', 'Range lot for this part is already registered (overlap).');
                    break;
                }
            }
        });

        return $validator->validate();
    }

    private function buildFilteredPlanQuery(Request $request)
    {
        $query = FgSwaPlan::query();
        $today = now()->format('Y-m-d');

        $dateFilter = (string) $request->input('date_filter', 'created_at');
        $dateFrom = substr((string) $request->input('date_from', $today), 0, 10);
        $dateTo = substr((string) $request->input('date_to', $today), 0, 10);

        if ($dateFilter === 'created_at') {
            if ($this->isDateString($dateFrom) && $this->isDateString($dateTo)) {
                if ($dateFrom > $dateTo) {
                    [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
                }

                $startExpr = $this->lotDateSqlExpression('start_lot_no');
                $endExpr = $this->lotDateSqlExpression('end_lot_no');
                $rangeStart = "LEAST($startExpr, $endExpr)";
                $rangeEnd = "GREATEST($startExpr, $endExpr)";

                $query->whereRaw("$rangeStart <= ? AND $rangeEnd >= ?", [$dateTo, $dateFrom]);
            }
        }

        $searchBy = (string) $request->input('search_by', '');
        $keyword = trim((string) $request->input('keyword', ''));
        $searchable = ['part_code', 'part_name', 'start_lot_no', 'end_lot_no'];

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

    private function attachTotalScan(Collection $plans): Collection
    {
        if ($plans->isEmpty()) {
            return $plans;
        }

        $planPartCodes = $plans
            ->pluck('part_code')
            ->filter()
            ->unique()
            ->values();

        $receivingRows = FgReceivingScan::query()
            ->whereIn('part_code', $planPartCodes)
            ->selectRaw('part_code, lot_no, SUM(qty_box) as total_qty')
            ->groupBy('part_code', 'lot_no')
            ->get();

        $deliveryRows = FgDeliveryScan::query()
            ->whereIn('part_code', $planPartCodes)
            ->selectRaw('part_code, lot_no, SUM(qty_box) as total_qty')
            ->groupBy('part_code', 'lot_no')
            ->get();

        $combinedRows = $receivingRows->concat($deliveryRows);
        $aggregatedByPartLot = [];
        foreach ($combinedRows as $scan) {
            $partKey = $this->normalizeLot((string) $scan->part_code);
            $lotKey = $this->normalizeLot((string) $scan->lot_no);
            if ($partKey === '' || $lotKey === '') {
                continue;
            }

            $key = $partKey . '|' . $lotKey;
            if (!isset($aggregatedByPartLot[$key])) {
                $aggregatedByPartLot[$key] = [
                    'part_key' => $partKey,
                    'lot_no' => (string) $scan->lot_no,
                    'total_qty' => 0,
                ];
            }
            $aggregatedByPartLot[$key]['total_qty'] += (int) $scan->total_qty;
        }

        $scansByPart = collect($aggregatedByPartLot)
            ->groupBy('part_key')
            ->map(function (Collection $rows) {
                return $rows->map(function (array $row) {
                    return (object) [
                        'lot_no' => $row['lot_no'],
                        'total_qty' => $row['total_qty'],
                    ];
                })->values();
            });

        $plans->transform(function (FgSwaPlan $plan) use ($scansByPart) {
            $partKey = $this->normalizeLot($plan->part_code);
            $plan->setAttribute(
                'total_scan',
                $this->calculateTotalScanForPlan($plan, $scansByPart->get($partKey, collect()))
            );

            return $plan;
        });

        return $plans;
    }

    private function calculateTotalScanForPlan(FgSwaPlan $plan, Collection $scanRows): int
    {
        $sum = 0;

        foreach ($scanRows as $scan) {
            if ($this->isLotWithinRange((string) $scan->lot_no, $plan->start_lot_no, $plan->end_lot_no)) {
                $sum += (int) $scan->total_qty;
            }
        }

        return $sum;
    }

    private function rangesOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        return $this->isLotWithinRange($startA, $startB, $endB)
            || $this->isLotWithinRange($endA, $startB, $endB)
            || $this->isLotWithinRange($startB, $startA, $endA);
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

    private function resolveSort(Request $request): array
    {
        $sortableColumns = [
            'created_at',
            'part_code',
            'part_name',
            'start_lot_no',
            'end_lot_no',
            'qty_box',
            'total_plan',
        ];

        $sortBy = (string) $request->input('sort_by', 'created_at');
        if (!in_array($sortBy, $sortableColumns, true)) {
            $sortBy = 'created_at';
        }

        $sortDir = strtolower((string) $request->input('sort_dir', 'desc'));
        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        return [$sortBy, $sortDir];
    }

    private function ensureWarehouseMutationPermission(Request $request): void
    {
        if (!$request->user()?->canManageWarehouseData()) {
            abort(403, 'Your role is not allowed to edit/delete warehouse data.');
        }
    }
}
