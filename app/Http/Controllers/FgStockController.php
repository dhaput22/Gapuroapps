<?php

namespace App\Http\Controllers;

use App\Models\FgReceivingScan;
use App\Models\Operator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FgStockController extends Controller
{
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
        $filteredQuery = $this->buildStockQuery($request);

        $scans = (clone $filteredQuery)
            ->orderBy($sortBy, $sortDir)
            ->when($sortBy !== 'id', fn($q) => $q->orderByDesc('id'))
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $summary = [
            'total_row' => $scans->total(),
            'total_qty' => (int) (clone $filteredQuery)->sum('qty_box'),
        ];

        return view('fg-storage.stock', compact('scans', 'summary', 'filters'));
    }

    public function edit(FgReceivingScan $scan): View
    {
        $operators = Operator::query()->orderBy('name')->get();
        return view('fg-storage.stock-edit', compact('scan', 'operators'));
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
            ->route('fg.storage.stock')
            ->with('success', 'Data FG Stock berhasil diperbarui.');
    }

    public function destroy(FgReceivingScan $scan): RedirectResponse
    {
        $scan->delete();

        return redirect()
            ->route('fg.storage.stock')
            ->with('success', 'Data FG Stock berhasil dihapus.');
    }

    private function buildStockQuery(Request $request)
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
        $searchable = ['part_code', 'part_name', 'lot_no'];

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

    private function isDateString(string $date): bool
    {
        return $date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
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
