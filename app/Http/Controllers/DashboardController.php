<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const TREND_DAYS = 7;
    private const RECEIVE_DATE_EXPRESSION = 'COALESCE(scanned_at, created_at)';
    private const DELIVERY_DATE_EXPRESSION = 'COALESCE(delivery_at, created_at)';

    public function index(): View
    {
        return view('dashboard', [
            'fgStorageMetrics' => $this->buildFgStorageMetrics(),
        ]);
    }

    public function fgStorageMetrics(): JsonResponse
    {
        return response()->json($this->buildFgStorageMetrics());
    }

    private function buildFgStorageMetrics(): array
    {
        $now = now();
        $today = $now->toDateString();
        $startDate = $now->copy()->subDays(self::TREND_DAYS - 1)->toDateString();
        $endDate = $today;

        $receivingToday = $this->dailySummary('fg_receiving_scans', self::RECEIVE_DATE_EXPRESSION, $today);
        $deliveryToday = $this->dailySummary('fg_delivery_scans', self::DELIVERY_DATE_EXPRESSION, $today);

        $receivingOpenTotal = $this->tableSummary('fg_receiving_scans');
        $deliveryTotal = $this->tableSummary('fg_delivery_scans');
        $totalReceive = $this->sumSummary($receivingOpenTotal, $deliveryTotal);

        // Stock formula utama: receive - delivery.
        $stockRows = max(0, $totalReceive['rows'] - $deliveryTotal['rows']);
        $stockQty = max(0, $totalReceive['qty'] - $deliveryTotal['qty']);

        $receivingByPartName = $this->summaryByPartName('fg_receiving_scans');
        $deliveryByPartName = $this->summaryByPartName('fg_delivery_scans');
        $itemTypeCapacities = $this->buildItemTypeCapacities($receivingByPartName, $deliveryByPartName);
        $configuredWarehouseCapacity = (int) config('fg_storage.warehouse_capacity_box', 0);
        $typeCapacityTotal = (int) collect($itemTypeCapacities)
            ->filter(fn(array $item) => (bool) ($item['has_capacity'] ?? false))
            ->sum(fn(array $item) => (int) ($item['capacity_qty'] ?? 0));

        $capacityQty = max(0, $configuredWarehouseCapacity > 0 ? $configuredWarehouseCapacity : $typeCapacityTotal);
        $availableQty = max(0, $capacityQty - $stockQty);
        $usedPercent = $capacityQty > 0 ? round(($stockQty / $capacityQty) * 100, 1) : 0.0;
        $usedPercentForBar = $capacityQty > 0 ? min(100, $usedPercent) : 0.0;

        $receivingDaily = $this->dailyRangeSummary(
            'fg_receiving_scans',
            self::RECEIVE_DATE_EXPRESSION,
            $startDate,
            $endDate
        );
        $receivingDaily = $this->mergeDailySummaries(
            $receivingDaily,
            $this->dailyRangeSummary(
                'fg_delivery_scans',
                self::RECEIVE_DATE_EXPRESSION,
                $startDate,
                $endDate
            )
        );
        $deliveryDaily = $this->dailyRangeSummary(
            'fg_delivery_scans',
            self::DELIVERY_DATE_EXPRESSION,
            $startDate,
            $endDate
        );

        $receivingBeforeStart = $this->sumSummary(
            $this->summaryBeforeDate('fg_receiving_scans', self::RECEIVE_DATE_EXPRESSION, $startDate),
            $this->summaryBeforeDate('fg_delivery_scans', self::RECEIVE_DATE_EXPRESSION, $startDate)
        );
        $deliveryBeforeStart = $this->summaryBeforeDate('fg_delivery_scans', self::DELIVERY_DATE_EXPRESSION, $startDate);
        $runningStockRows = $receivingBeforeStart['rows'] - $deliveryBeforeStart['rows'];
        $runningStockQty = $receivingBeforeStart['qty'] - $deliveryBeforeStart['qty'];

        $dailyFlow = [];
        for ($offset = self::TREND_DAYS - 1; $offset >= 0; $offset--) {
            $date = $now->copy()->subDays($offset)->toDateString();

            $dailyReceive = $receivingDaily[$date] ?? ['rows' => 0, 'qty' => 0];
            $dailyDelivery = $deliveryDaily[$date] ?? ['rows' => 0, 'qty' => 0];
            $netRows = (int) $dailyReceive['rows'] - (int) $dailyDelivery['rows'];
            $netQty = (int) $dailyReceive['qty'] - (int) $dailyDelivery['qty'];
            $runningStockRows += $netRows;
            $runningStockQty += $netQty;

            $dailyFlow[] = [
                'date' => $date,
                'receiving_rows' => (int) $dailyReceive['rows'],
                'receiving_qty' => (int) $dailyReceive['qty'],
                'delivery_rows' => (int) $dailyDelivery['rows'],
                'delivery_qty' => (int) $dailyDelivery['qty'],
                'net_qty' => $netQty,
                'stock_rows' => max(0, $runningStockRows),
                'stock_qty' => max(0, $runningStockQty),
            ];
        }

        return [
            'as_of' => $now->toIso8601String(),
            'today' => [
                'receiving' => $receivingToday,
                'delivery' => $deliveryToday,
            ],
            'stock' => [
                'rows' => $stockRows,
                'qty' => $stockQty,
                'capacity_qty' => $capacityQty,
                'available_qty' => $availableQty,
                'used_percent' => $usedPercent,
                'used_percent_for_bar' => $usedPercentForBar,
                'over_capacity' => $capacityQty > 0 && $stockQty > $capacityQty,
            ],
            'totals' => [
                'receive' => $totalReceive,
                'delivery' => $deliveryTotal,
            ],
            'item_type_capacities' => $itemTypeCapacities,
            'daily_flow' => $dailyFlow,
            'meta' => [
                'polling_seconds' => max(5, (int) config('fg_storage.dashboard_polling_seconds', 15)),
                'trend_days' => self::TREND_DAYS,
                'capacity_unit' => 'box',
            ],
        ];
    }

    private function dailySummary(string $table, string $dateExpression, string $day): array
    {
        $summary = DB::table($table)
            ->selectRaw('COUNT(*) as total_rows')
            ->selectRaw('COALESCE(SUM(qty_box), 0) as total_qty')
            ->whereRaw("DATE($dateExpression) = ?", [$day])
            ->first();

        return [
            'rows' => (int) ($summary->total_rows ?? 0),
            'qty' => (int) ($summary->total_qty ?? 0),
        ];
    }

    private function dailyRangeSummary(string $table, string $dateExpression, string $startDate, string $endDate): array
    {
        $rows = DB::table($table)
            ->selectRaw("DATE($dateExpression) as summary_date")
            ->selectRaw('COUNT(*) as total_rows')
            ->selectRaw('COALESCE(SUM(qty_box), 0) as total_qty')
            ->whereRaw("DATE($dateExpression) BETWEEN ? AND ?", [$startDate, $endDate])
            ->groupByRaw("DATE($dateExpression)")
            ->orderBy('summary_date')
            ->get();

        $mapped = [];
        foreach ($rows as $row) {
            $date = (string) ($row->summary_date ?? '');
            if ($date === '') {
                continue;
            }

            $mapped[$date] = [
                'rows' => (int) ($row->total_rows ?? 0),
                'qty' => (int) ($row->total_qty ?? 0),
            ];
        }

        return $mapped;
    }

    private function buildItemTypeCapacities(Collection $receivingByPartName, Collection $deliveryByPartName): array
    {
        $definitions = $this->itemTypeDefinitions();
        if ($definitions === []) {
            return [];
        }

        $stats = [];
        foreach ($definitions as $definition) {
            $stats[$definition['key']] = [
                'key' => $definition['key'],
                'label' => $definition['label'],
                'capacity_qty' => (int) $definition['capacity_qty'],
                'receive_qty' => 0,
                'receive_rows' => 0,
                'delivery_qty' => 0,
                'delivery_rows' => 0,
            ];
        }

        $unmapped = [
            'receive_qty' => 0,
            'receive_rows' => 0,
            'delivery_qty' => 0,
            'delivery_rows' => 0,
        ];

        // Receive total = data receiving + data delivery (sebagai histori receiving yang sudah keluar).
        $this->applyPartSummaryToTypeStats($stats, $definitions, $receivingByPartName, $unmapped, 'receive');
        $this->applyPartSummaryToTypeStats($stats, $definitions, $deliveryByPartName, $unmapped, 'receive');
        $this->applyPartSummaryToTypeStats($stats, $definitions, $deliveryByPartName, $unmapped, 'delivery');

        $result = [];
        foreach ($definitions as $definition) {
            $key = $definition['key'];
            $itemStat = $stats[$key];
            $capacityQty = (int) $itemStat['capacity_qty'];
            $receiveQty = (int) $itemStat['receive_qty'];
            $deliveryQty = (int) $itemStat['delivery_qty'];
            $stockQty = max(0, $receiveQty - $deliveryQty);
            $receiveRows = (int) $itemStat['receive_rows'];
            $deliveryRows = (int) $itemStat['delivery_rows'];
            $stockRows = max(0, $receiveRows - $deliveryRows);
            $remainingQty = $capacityQty - $stockQty;
            $usedPercent = $capacityQty > 0 ? round(($stockQty / $capacityQty) * 100, 1) : 0.0;

            $result[] = [
                'key' => $key,
                'label' => $itemStat['label'],
                'receive_qty' => $receiveQty,
                'delivery_qty' => $deliveryQty,
                'stock_qty' => $stockQty,
                'stock_rows' => $stockRows,
                'capacity_qty' => $capacityQty,
                'available_qty' => max(0, $remainingQty),
                'remaining_qty' => $remainingQty,
                'excess_qty' => max(0, -$remainingQty),
                'used_percent' => $usedPercent,
                'used_percent_for_bar' => min(100, $usedPercent),
                'over_capacity' => $capacityQty > 0 && $stockQty > $capacityQty,
                'has_capacity' => true,
            ];
        }

        $unmappedStockQty = max(0, (int) $unmapped['receive_qty'] - (int) $unmapped['delivery_qty']);
        $unmappedRows = max(0, (int) $unmapped['receive_rows'] - (int) $unmapped['delivery_rows']);
        if ($unmappedRows > 0 || $unmappedStockQty > 0 || (int) $unmapped['delivery_rows'] > 0 || (int) $unmapped['delivery_qty'] > 0) {
            $result[] = [
                'key' => 'unmapped_part_name',
                'label' => 'UNMAPPED PART NAME',
                'receive_qty' => (int) $unmapped['receive_qty'],
                'delivery_qty' => (int) $unmapped['delivery_qty'],
                'stock_qty' => $unmappedStockQty,
                'stock_rows' => $unmappedRows,
                'capacity_qty' => null,
                'available_qty' => null,
                'remaining_qty' => null,
                'excess_qty' => null,
                'used_percent' => null,
                'used_percent_for_bar' => null,
                'over_capacity' => false,
                'has_capacity' => false,
            ];
        }

        return $result;
    }

    private function applyPartSummaryToTypeStats(
        array &$stats,
        array $definitions,
        Collection $partSummaryRows,
        array &$unmapped,
        string $bucket
    ): void {
        foreach ($partSummaryRows as $row) {
            $partName = (string) ($row->part_name ?? '');
            $rows = max(0, (int) ($row->total_rows ?? 0));
            $qty = max(0, (int) ($row->total_qty ?? 0));
            if ($rows === 0 && $qty === 0) {
                continue;
            }

            $matchedKey = $this->resolveItemTypeKeyFromPartName($partName, $definitions);
            $rowsKey = $bucket . '_rows';
            $qtyKey = $bucket . '_qty';

            if ($matchedKey === null || !isset($stats[$matchedKey])) {
                $unmapped[$rowsKey] += $rows;
                $unmapped[$qtyKey] += $qty;
                continue;
            }

            $stats[$matchedKey][$rowsKey] += $rows;
            $stats[$matchedKey][$qtyKey] += $qty;
        }
    }

    private function itemTypeDefinitions(): array
    {
        $configured = collect(config('fg_storage.item_type_capacities', []))
            ->filter(fn($item) => is_array($item))
            ->values();

        if ($configured->isEmpty()) {
            return [];
        }

        $definitions = [];
        foreach ($configured as $index => $item) {
            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $key = trim((string) ($item['key'] ?? ''));
            if ($key === '') {
                $key = 'type_' . ($index + 1);
            }

            $normalizedAliases = collect((array) ($item['aliases'] ?? []))
                ->push($label)
                ->filter(fn($alias) => trim((string) $alias) !== '')
                ->map(fn($alias) => $this->normalizeMatcherText((string) $alias))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $definitions[] = [
                'key' => $key,
                'label' => $label,
                'capacity_qty' => max(0, (int) ($item['capacity_box'] ?? 0)),
                'normalized_aliases' => $normalizedAliases,
            ];
        }

        return $definitions;
    }

    private function resolveItemTypeKeyFromPartName(string $partName, array $definitions): ?string
    {
        $normalizedPartName = $this->normalizeMatcherText($partName);
        if ($normalizedPartName === '') {
            return null;
        }

        foreach ($definitions as $definition) {
            $aliases = (array) ($definition['normalized_aliases'] ?? []);
            foreach ($aliases as $alias) {
                if ($alias === '') {
                    continue;
                }

                if (str_contains($normalizedPartName, $alias)) {
                    return (string) $definition['key'];
                }
            }
        }

        return null;
    }

    private function normalizeMatcherText(string $value): string
    {
        return strtoupper((string) preg_replace('/[^A-Z0-9]/i', '', trim($value)));
    }

    private function mergeDailySummaries(array $first, array $second): array
    {
        $dates = array_values(array_unique(array_merge(array_keys($first), array_keys($second))));
        sort($dates);

        $result = [];
        foreach ($dates as $date) {
            $result[$date] = $this->sumSummary(
                $first[$date] ?? ['rows' => 0, 'qty' => 0],
                $second[$date] ?? ['rows' => 0, 'qty' => 0]
            );
        }

        return $result;
    }

    private function sumSummary(array $left, array $right): array
    {
        return [
            'rows' => max(0, (int) ($left['rows'] ?? 0) + (int) ($right['rows'] ?? 0)),
            'qty' => (int) ($left['qty'] ?? 0) + (int) ($right['qty'] ?? 0),
        ];
    }

    private function tableSummary(string $table): array
    {
        $summary = DB::table($table)
            ->selectRaw('COUNT(*) as total_rows')
            ->selectRaw('COALESCE(SUM(qty_box), 0) as total_qty')
            ->first();

        return [
            'rows' => (int) ($summary->total_rows ?? 0),
            'qty' => (int) ($summary->total_qty ?? 0),
        ];
    }

    private function summaryBeforeDate(string $table, string $dateExpression, string $date): array
    {
        $summary = DB::table($table)
            ->selectRaw('COUNT(*) as total_rows')
            ->selectRaw('COALESCE(SUM(qty_box), 0) as total_qty')
            ->whereRaw("DATE($dateExpression) < ?", [$date])
            ->first();

        return [
            'rows' => (int) ($summary->total_rows ?? 0),
            'qty' => (int) ($summary->total_qty ?? 0),
        ];
    }

    private function summaryByPartName(string $table): Collection
    {
        return DB::table($table)
            ->selectRaw('COALESCE(part_name, "") as part_name')
            ->selectRaw('COUNT(*) as total_rows')
            ->selectRaw('COALESCE(SUM(qty_box), 0) as total_qty')
            ->groupBy('part_name')
            ->get();
    }
}
