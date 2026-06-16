<?php

namespace App\Http\Controllers;

use App\Models\FgDeliveryScan;
use App\Models\FgReturnScan;
use App\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FgReturnController extends Controller
{
    private const RETURN_SCAN_IDS_SESSION_KEY = 'fg_return_scan_ids';

    public function index(Request $request): View
    {
        $today = now()->format('Y-m-d');
        [$sortBy, $sortDir] = $this->resolveSort(
            $request,
            ['return_at', 'part_code', 'part_name', 'lot_no', 'qty_box', 'created_at'],
            'return_at'
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
        $filteredQuery = $this->buildReturnQuery($request);

        $scans = (clone $filteredQuery)
            ->orderBy($sortBy, $sortDir)
            ->when($sortBy !== 'id', fn($query) => $query->orderByDesc('id'))
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $summary = [
            'total_row' => $scans->total(),
            'total_qty' => (int) (clone $filteredQuery)->sum('qty_box'),
        ];

        return view('fg-storage.return', compact('scans', 'summary', 'filters'));
    }

    public function create(Request $request): View
    {
        [$sortBy, $sortDir] = $this->resolveSort(
            $request,
            ['return_at', 'part_code', 'part_name', 'lot_no', 'qty_box', 'created_at'],
            'return_at'
        );

        $filters = [
            'return_date' => (string) $request->input('return_date', now()->format('Y-m-d')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ];
        $defaultPartCode = trim((string) $request->query('part_code', ''));
        $defaultOperatorEmployeeId = trim((string) $request->query('operator_employee_id', ''));

        if (!$request->boolean('carry')) {
            $request->session()->forget(self::RETURN_SCAN_IDS_SESSION_KEY);
        }

        $scanIds = collect((array) $request->session()->get(self::RETURN_SCAN_IDS_SESSION_KEY, []))
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $page = max(1, (int) $request->input('page', 1));
        $query = FgReturnScan::query()
            ->with(['operator', 'returnOperator'])
            ->whereIn('id', $scanIds);

        $scans = (clone $query)
            ->orderBy($sortBy, $sortDir)
            ->when($sortBy !== 'id', fn($builder) => $builder->orderByDesc('id'))
            ->paginate($filters['page_size'], ['*'], 'page', $page)
            ->withQueryString();

        $totalCount = (clone $query)->count();

        return view('fg-storage.return-create', compact('filters', 'scans', 'totalCount', 'defaultPartCode', 'defaultOperatorEmployeeId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'operator_employee_id' => ['required', 'string', 'max:50'],
            'part_code' => ['required', 'string', 'max:100'],
            'lot_no' => ['required', 'string', 'max:100'],
            'return_date' => ['nullable', 'date'],
            'remark' => ['nullable', 'string', 'max:255'],
        ]);

        $operatorEmployeeId = trim($validated['operator_employee_id']);
        $partCode = trim($validated['part_code']);
        $lotNo = trim($validated['lot_no']);
        $returnDate = (string) ($validated['return_date'] ?? now()->format('Y-m-d'));
        $remark = trim((string) ($validated['remark'] ?? ''));

        $operator = Operator::query()->where('employee_id', $operatorEmployeeId)->first();
        if (!$operator) {
            return back()
                ->withErrors(['operator_employee_id' => 'Nomor ID operator tidak ditemukan.'])
                ->withInput();
        }

        if ($this->isDuplicateReturn($partCode, $lotNo)) {
            return back()
                ->withErrors(['lot_no' => 'Lot No ini sudah ada di FG Return.'])
                ->withInput();
        }

        $deliveryScan = $this->findDeliveryScanByPartAndLot($partCode, $lotNo);

        if ($deliveryScan !== null) {
            $returnScan = FgReturnScan::query()->create([
                'label_id' => $deliveryScan->label_id,
                'part_code' => $deliveryScan->part_code,
                'part_name' => $deliveryScan->part_name,
                'lot_no' => $deliveryScan->lot_no,
                'qty_box' => (int) $deliveryScan->qty_box,
                'scanned_at' => $deliveryScan->scanned_at,
                'operator_id' => $deliveryScan->operator_id,
                'created_by' => $request->user()?->id,
                'return_at' => $this->isDateString($returnDate) ? $returnDate . ' ' . now()->format('H:i:s') : now(),
                'return_operator_id' => $operator->id,
                'remark' => $remark !== '' ? $remark : null,
            ]);

            $deliveryScan->delete();

            $this->pushToSession($request, (int) $returnScan->id);

            return redirect()
                ->route('fg.storage.return.create', [
                    'return_date' => $returnDate,
                    'carry' => 1,
                    'part_code' => $returnScan->part_code,
                    'operator_employee_id' => $operator->employee_id,
                ])
                ->with('success', 'Return berhasil. Barang dipindahkan dari FG Delivery ke FG Return.');
        }

        // Item tidak ada di delivery — return langsung (unregistered)
        $returnScan = FgReturnScan::query()->create([
            'part_code' => $partCode,
            'part_name' => null,
            'lot_no' => $lotNo,
            'qty_box' => 0,
            'return_at' => $this->isDateString($returnDate) ? $returnDate . ' ' . now()->format('H:i:s') : now(),
            'return_operator_id' => $operator->id,
            'created_by' => $request->user()?->id,
            'remark' => $remark !== '' ? $remark : null,
        ]);

        $this->pushToSession($request, (int) $returnScan->id);

        return redirect()
            ->route('fg.storage.return.create', [
                'return_date' => $returnDate,
                'carry' => 1,
                'part_code' => $partCode,
                'operator_employee_id' => $operator->employee_id,
            ])
            ->with('success', 'Return berhasil diregister ke FG Return.');
    }

    public function edit(FgReturnScan $scan): View
    {
        $operators = Operator::query()->orderBy('name')->get();
        return view('fg-storage.return-edit', compact('scan', 'operators'));
    }

    public function update(Request $request, FgReturnScan $scan): RedirectResponse
    {
        $validated = $request->validate([
            'part_code' => ['required', 'string', 'max:100'],
            'part_name' => ['nullable', 'string', 'max:255'],
            'lot_no' => ['required', 'string', 'max:100'],
            'qty_box' => ['required', 'integer', 'min:0'],
            'return_at' => ['nullable', 'date'],
            'return_operator_id' => ['nullable', 'exists:operators,id'],
            'remark' => ['nullable', 'string', 'max:255'],
        ]);

        $scan->update([
            'part_code' => trim($validated['part_code']),
            'part_name' => isset($validated['part_name']) ? trim($validated['part_name']) : $scan->part_name,
            'lot_no' => trim($validated['lot_no']),
            'qty_box' => (int) $validated['qty_box'],
            'return_at' => $validated['return_at'] ?? $scan->return_at,
            'return_operator_id' => $validated['return_operator_id'] ?? $scan->return_operator_id,
            'remark' => isset($validated['remark']) ? (trim($validated['remark']) !== '' ? trim($validated['remark']) : null) : $scan->remark,
        ]);

        return redirect()
            ->route('fg.storage.return.index')
            ->with('success', 'Data FG Return berhasil diperbarui.');
    }

    public function destroy(FgReturnScan $scan): RedirectResponse
    {
        $scan->delete();

        return redirect()
            ->route('fg.storage.return.index')
            ->with('success', 'Data FG Return berhasil dihapus.');
    }

    public function previewPart(Request $request): JsonResponse
    {
        $partCode = trim((string) $request->query('part_code', ''));
        if ($partCode === '') {
            return response()->json(['message' => 'Part Code wajib diisi.'], 422);
        }

        $deliveryScan = $this->findDeliveryScanByPartCode($partCode);
        if ($deliveryScan !== null) {
            return response()->json([
                'part_code' => $deliveryScan->part_code,
                'part_name' => $deliveryScan->part_name,
                'qty_box' => (int) $deliveryScan->qty_box,
                'source' => 'delivery',
                'message' => 'Part ditemukan di FG Delivery. Lanjut scan Lot No.',
            ]);
        }

        return response()->json([
            'message' => 'Part Code tidak ditemukan di FG Delivery. Part ini belum pernah dikirim atau sudah di-return.',
        ], 422);
    }

    public function previewScan(Request $request): JsonResponse
    {
        $partCode = trim((string) $request->query('part_code', ''));
        $lotNo = trim((string) $request->query('lot_no', ''));

        if ($partCode === '' || $lotNo === '') {
            return response()->json(['message' => 'Part Code dan Lot No wajib diisi.'], 422);
        }

        if ($this->isDuplicateReturn($partCode, $lotNo)) {
            return response()->json(['message' => 'Lot No ini sudah ada di FG Return.'], 422);
        }

        $deliveryScan = $this->findDeliveryScanByPartAndLot($partCode, $lotNo);
        if ($deliveryScan !== null) {
            return response()->json([
                'part_code' => $deliveryScan->part_code,
                'part_name' => $deliveryScan->part_name,
                'lot_no' => $deliveryScan->lot_no,
                'qty_box' => (int) $deliveryScan->qty_box,
                'source' => 'delivery',
                'action' => 'RETURN_FROM_DELIVERY',
                'message' => 'Lot ditemukan di FG Delivery. Submit untuk memindahkan ke FG Return.',
            ]);
        }

        return response()->json([
            'message' => 'Kombinasi Part Code dan Lot No tidak ditemukan di FG Delivery.',
        ], 422);
    }

    private function buildReturnQuery(Request $request)
    {
        $query = FgReturnScan::query()->with(['operator', 'returnOperator']);

        $today = now()->format('Y-m-d');
        $dateFrom = (string) $request->input('date_from', $today);
        $dateTo = (string) $request->input('date_to', $today);
        if ($this->isDateString($dateFrom) && $this->isDateString($dateTo)) {
            if ($dateFrom > $dateTo) {
                [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
            }
            $query->whereRaw("DATE(COALESCE(return_at, created_at)) BETWEEN ? AND ?", [$dateFrom, $dateTo]);
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

    private function isDuplicateReturn(string $partCode, string $lotNo): bool
    {
        $normalizedPartCode = $this->normalizeCode($partCode);
        $normalizedLot = $this->normalizeLot($lotNo);
        if ($normalizedPartCode === '' || $normalizedLot === '') {
            return false;
        }

        return FgReturnScan::query()
            ->where('part_code', $partCode)
            ->get(['lot_no'])
            ->contains(function (FgReturnScan $scan) use ($normalizedLot) {
                return $this->normalizeLot((string) $scan->lot_no) === $normalizedLot;
            });
    }

    private function pushToSession(Request $request, int $id): void
    {
        $ids = collect((array) $request->session()->get(self::RETURN_SCAN_IDS_SESSION_KEY, []))
            ->map(fn($v) => (int) $v)
            ->filter(fn(int $v) => $v > 0)
            ->push($id)
            ->unique()
            ->values()
            ->all();

        $request->session()->put(self::RETURN_SCAN_IDS_SESSION_KEY, $ids);
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
