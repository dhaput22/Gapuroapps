<?php

namespace App\Http\Controllers;

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

        $filters = [
            'date_filter' => (string) $request->input('date_filter', 'created_at'),
            'date_from' => (string) $request->input('date_from', $today),
            'date_to' => (string) $request->input('date_to', $today),
            'search_by' => (string) $request->input('search_by', ''),
            'keyword' => trim((string) $request->input('keyword', '')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
        ];

        $page = max(1, (int) $request->input('page', $request->input('page_no', 1)));
        $filteredQuery = $this->buildFilteredPlanQuery($request);

        $plans = (clone $filteredQuery)
            ->latest('id')
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

    public function edit(FgSwaPlan $plan): View
    {
        return view('fg-storage.swa-form', [
            'plan' => $plan,
            'mode' => 'edit',
            'formAction' => route('fg.storage.swa.update', $plan),
            'httpMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, FgSwaPlan $plan): RedirectResponse
    {
        $validated = $this->validatePlanPayload($request, $plan->id);

        $plan->update($validated);

        return redirect()
            ->route('fg.storage.swa')
            ->with('success', 'Plan FG for SWA berhasil diperbarui.');
    }

    public function destroy(FgSwaPlan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()
            ->route('fg.storage.swa')
            ->with('success', 'Plan FG for SWA berhasil dihapus.');
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
                $validator->errors()->add('end_lot_no', 'End Lot No harus lebih besar atau sama dengan Start Lot No.');
                return;
            }

            $query = FgSwaPlan::query()->where('part_code', $partCode);
            if ($exceptId !== null) {
                $query->where('id', '!=', $exceptId);
            }

            $ranges = $query->get(['start_lot_no', 'end_lot_no']);
            foreach ($ranges as $range) {
                if ($this->rangesOverlap($startLot, $endLot, $range->start_lot_no, $range->end_lot_no)) {
                    $validator->errors()->add('start_lot_no', 'Range lot untuk part ini sudah terdaftar (overlap).');
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
        $dateFrom = (string) $request->input('date_from', $today);
        $dateTo = (string) $request->input('date_to', $today);

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

        $scansByPart = FgReceivingScan::query()
            ->whereIn('part_code', $planPartCodes)
            ->selectRaw('part_code, lot_no, SUM(qty_box) as total_qty')
            ->groupBy('part_code', 'lot_no')
            ->get()
            ->groupBy(fn(FgReceivingScan $scan) => $this->normalizeLot((string) $scan->part_code));

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
}
