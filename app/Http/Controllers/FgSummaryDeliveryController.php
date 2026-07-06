<?php

namespace App\Http\Controllers;

use App\Models\FgDeliveryScan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FgSummaryDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->format('Y-m-d');

        $filters = [
            'date_from' => $request->input('date_from', $today),
            'date_to'   => $request->input('date_to', $today),
            'search_by' => $request->input('search_by', ''),
            'keyword'   => trim((string) $request->input('keyword', '')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 20))),
            'sort_by'   => $request->input('sort_by', 'delivery_date'),
            'sort_dir'  => strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc',
        ];

        [$sortBy, $sortDir] = $this->resolveSort($filters['sort_by'], $filters['sort_dir']);

        $query = $this->buildGroupedQuery($filters);
        $query->orderBy($sortBy, $sortDir);
        if ($sortBy !== 'part_code') {
            $query->orderBy('part_code', 'asc');
        }

        $scans = $query->paginate($filters['page_size'], ['*'], 'page');

        $totalQtyQuery = FgDeliveryScan::query();
        $this->applyFilters($totalQtyQuery, $filters);
        $totalQty = (int) $totalQtyQuery->sum('qty_box');

        $summary = [
            'total_row' => $scans->total(),
            'total_qty' => $totalQty,
        ];

        return view('fg-storage.summary-delivery', compact('scans', 'filters', 'summary'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $today = now()->format('Y-m-d');

        $filters = [
            'date_from' => $request->input('date_from', ''),
            'date_to'   => $request->input('date_to', ''),
            'search_by' => $request->input('search_by', ''),
            'keyword'   => trim((string) $request->input('keyword', '')),
            'sort_by'   => $request->input('sort_by', 'delivery_date'),
            'sort_dir'  => strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc',
        ];

        [$sortBy, $sortDir] = $this->resolveSort($filters['sort_by'], $filters['sort_dir']);

        $rows = $this->buildGroupedQuery($filters)
            ->orderBy($sortBy, $sortDir)
            ->orderBy('part_code', 'asc')
            ->get();

        $grandTotalScan = $rows->sum('total_scan');
        $grandTotalQty  = $rows->sum('total_qty');

        $periodFrom = $filters['date_from'] ?: '-';
        $periodTo   = $filters['date_to']   ?: '-';
        $exportedAt = now()->format('Y-m-d H:i:s');

        $filename = 'summary-delivery-' . $today . '.xls';

        return response()->streamDownload(function () use ($rows, $grandTotalScan, $grandTotalQty, $periodFrom, $periodTo, $exportedAt) {
            $th = 'style="background-color:#FFD700;font-weight:bold;text-align:center;border:1px solid #999;padding:6px 10px;white-space:nowrap;"';

            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;">';

            // Report title
            echo '<tr><td colspan="6" style="font-size:15px;font-weight:bold;padding:8px 10px;text-align:center;">SUMMARY DELIVERY REPORT</td></tr>';
            echo '<tr><td colspan="6" style="padding:2px 10px;text-align:center;color:#555;">FG Storage</td></tr>';
            echo '<tr><td colspan="6" style="padding:4px;"></td></tr>';

            // Filter info
            echo '<tr>';
            echo '<td colspan="2" style="padding:3px 10px;font-weight:bold;">Periode</td>';
            echo '<td colspan="4" style="padding:3px 10px;">' . htmlspecialchars($periodFrom) . ' &nbsp;s/d&nbsp; ' . htmlspecialchars($periodTo) . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="2" style="padding:3px 10px;font-weight:bold;">Tanggal Export</td>';
            echo '<td colspan="4" style="padding:3px 10px;">' . htmlspecialchars($exportedAt) . '</td>';
            echo '</tr>';
            echo '<tr><td colspan="6" style="padding:6px;"></td></tr>';

            // Column headers
            echo '<tr>';
            echo "<th $th>No</th>";
            echo "<th $th>Delivery Date</th>";
            echo "<th $th>Part Code</th>";
            echo "<th $th>Part Name</th>";
            echo "<th $th>Total Scan</th>";
            echo "<th $th>Total Qty (Box)</th>";
            echo '</tr>';

            // Data rows
            foreach ($rows as $i => $row) {
                $bg = $i % 2 === 0 ? '#FFFFF0' : '#FFFFFF';
                $tdRow   = 'style="border:1px solid #ccc;padding:5px 10px;background-color:' . $bg . ';"';
                $tdRowNum = 'style="border:1px solid #ccc;padding:5px 10px;text-align:right;background-color:' . $bg . ';"';
                $tdRowCtr = 'style="border:1px solid #ccc;padding:5px 10px;text-align:center;background-color:' . $bg . ';"';

                echo '<tr>';
                echo "<td $tdRowCtr>" . ($i + 1) . '</td>';
                echo "<td $tdRow>" . htmlspecialchars((string) $row->delivery_date) . '</td>';
                echo "<td $tdRow>" . htmlspecialchars((string) $row->part_code) . '</td>';
                echo "<td $tdRow>" . htmlspecialchars((string) ($row->part_name ?? '-')) . '</td>';
                echo "<td $tdRowNum>" . number_format((int) $row->total_scan) . '</td>';
                echo "<td $tdRowNum>" . number_format((int) $row->total_qty) . '</td>';
                echo '</tr>';
            }

            // Total row
            $tdTotal = 'style="border:1px solid #999;padding:6px 10px;background-color:#FFF3CD;font-weight:bold;"';
            $tdTotalNum = 'style="border:1px solid #999;padding:6px 10px;text-align:right;background-color:#FFF3CD;font-weight:bold;"';
            echo '<tr>';
            echo "<td colspan=\"4\" $tdTotal style=\"border:1px solid #999;padding:6px 10px;background-color:#FFF3CD;font-weight:bold;text-align:right;\">TOTAL</td>";
            echo "<td $tdTotalNum>" . number_format((int) $grandTotalScan) . '</td>';
            echo "<td $tdTotalNum>" . number_format((int) $grandTotalQty) . '</td>';
            echo '</tr>';

            echo '</table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function buildGroupedQuery(array $filters): Builder
    {
        $query = FgDeliveryScan::query()
            ->select(
                DB::raw('DATE(COALESCE(delivery_at, created_at)) as delivery_date'),
                'part_code',
                'part_name',
                DB::raw('COUNT(*) as total_scan'),
                DB::raw('SUM(qty_box) as total_qty')
            )
            ->groupBy(DB::raw('DATE(COALESCE(delivery_at, created_at))'), 'part_code', 'part_name');

        $this->applyFilters($query, $filters);

        return $query;
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        $dateFrom = substr((string) ($filters['date_from'] ?? ''), 0, 10);
        $dateTo   = substr((string) ($filters['date_to'] ?? ''), 0, 10);
        if ($dateFrom !== '') {
            $query->whereRaw('DATE(COALESCE(delivery_at, created_at)) >= ?', [$dateFrom]);
        }
        if ($dateTo !== '') {
            $query->whereRaw('DATE(COALESCE(delivery_at, created_at)) <= ?', [$dateTo]);
        }
        if ($filters['search_by'] !== '' && $filters['keyword'] !== '') {
            $col = in_array($filters['search_by'], ['part_code', 'part_name'], true)
                ? $filters['search_by']
                : 'part_code';
            $query->where($col, 'like', '%' . $filters['keyword'] . '%');
        }
    }

    private function resolveSort(string $sortBy, string $sortDir): array
    {
        $allowed = ['delivery_date', 'part_code', 'part_name', 'total_scan', 'total_qty'];
        $sortBy  = in_array($sortBy, $allowed, true) ? $sortBy : 'delivery_date';
        return [$sortBy, $sortDir];
    }
}
