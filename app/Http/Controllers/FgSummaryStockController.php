<?php

namespace App\Http\Controllers;

use App\Models\FgReceivingScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FgSummaryStockController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from', ''),
            'date_to'   => $request->input('date_to', ''),
            'search_by' => $request->input('search_by', ''),
            'keyword'   => $request->input('keyword', ''),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 20))),
            'sort_by'   => $request->input('sort_by', 'part_code'),
            'sort_dir'  => strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc',
        ];

        [$sortBy, $sortDir] = $this->resolveSort($filters['sort_by'], $filters['sort_dir']);

        $query = FgReceivingScan::query()
            ->select(
                'part_code',
                'part_name',
                DB::raw('COUNT(*) as total_scan'),
                DB::raw('SUM(qty_box) as total_qty')
            )
            ->groupBy('part_code', 'part_name');

        $this->applyFilters($query, $filters);

        $query->orderBy($sortBy, $sortDir);

        $scans = $query->paginate($filters['page_size'], ['*'], 'page');

        $totalQtyQuery = FgReceivingScan::query();
        $this->applyFilters($totalQtyQuery, $filters);
        $totalQty = (int) $totalQtyQuery->sum('qty_box');

        $summary = [
            'total_row' => $scans->total(),
            'total_qty' => $totalQty,
        ];

        return view('fg-storage.summary-stock', compact('scans', 'filters', 'summary'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $filters = [
            'search_by' => $request->input('search_by', ''),
            'keyword'   => trim((string) $request->input('keyword', '')),
            'sort_by'   => $request->input('sort_by', 'part_code'),
            'sort_dir'  => strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc',
        ];

        [$sortBy, $sortDir] = $this->resolveSort($filters['sort_by'], $filters['sort_dir']);

        $rows = FgReceivingScan::query()
            ->select(
                'part_code',
                'part_name',
                DB::raw('COUNT(*) as total_scan'),
                DB::raw('SUM(qty_box) as total_qty')
            )
            ->groupBy('part_code', 'part_name');

        if ($filters['search_by'] !== '' && $filters['keyword'] !== '') {
            $col = in_array($filters['search_by'], ['part_code', 'part_name'], true)
                ? $filters['search_by']
                : 'part_code';
            $rows->where($col, 'like', '%' . $filters['keyword'] . '%');
        }

        $rows->orderBy($sortBy, $sortDir);
        $rows = $rows->get();

        $grandTotalScan = $rows->sum('total_scan');
        $grandTotalQty  = $rows->sum('total_qty');

        $exportedAt = now()->format('Y-m-d H:i:s');

        $filename = 'summary-stock-' . now()->format('Y-m-d') . '.xls';

        return response()->streamDownload(function () use ($rows, $grandTotalScan, $grandTotalQty, $exportedAt) {
            $th = 'style="background-color:#FFD700;font-weight:bold;text-align:center;border:1px solid #999;padding:6px 10px;white-space:nowrap;"';

            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;">';

            // Report title
            echo '<tr><td colspan="5" style="font-size:15px;font-weight:bold;padding:8px 10px;text-align:center;">SUMMARY STOCK REPORT</td></tr>';
            echo '<tr><td colspan="5" style="padding:2px 10px;text-align:center;color:#555;">FG Storage - Semua Stock yang Tersimpan di Gudang</td></tr>';
            echo '<tr><td colspan="5" style="padding:4px;"></td></tr>';

            // Export info
            echo '<tr>';
            echo '<td colspan="2" style="padding:3px 10px;font-weight:bold;">Tanggal Export</td>';
            echo '<td colspan="3" style="padding:3px 10px;">' . htmlspecialchars($exportedAt) . '</td>';
            echo '</tr>';
            echo '<tr><td colspan="5" style="padding:6px;"></td></tr>';

            // Column headers
            echo '<tr>';
            echo "<th $th>No</th>";
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
            echo "<td colspan=\"3\" $tdTotal style=\"border:1px solid #999;padding:6px 10px;background-color:#FFF3CD;font-weight:bold;text-align:right;\">TOTAL</td>";
            echo "<td $tdTotalNum>" . number_format((int) $grandTotalScan) . '</td>';
            echo "<td $tdTotalNum>" . number_format((int) $grandTotalQty) . '</td>';
            echo '</tr>';

            echo '</table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        $dateFrom = substr((string) ($filters['date_from'] ?? ''), 0, 10);
        $dateTo   = substr((string) ($filters['date_to'] ?? ''), 0, 10);
        if ($dateFrom !== '') {
            $query->whereRaw('DATE(scanned_at) >= ?', [$dateFrom]);
        }
        if ($dateTo !== '') {
            $query->whereRaw('DATE(scanned_at) <= ?', [$dateTo]);
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
        $allowed = ['part_code', 'part_name', 'total_scan', 'total_qty'];
        $sortBy  = in_array($sortBy, $allowed, true) ? $sortBy : 'part_code';
        return [$sortBy, $sortDir];
    }
}
