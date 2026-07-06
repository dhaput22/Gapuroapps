<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dummy data 10 item type, 7 hari kebelakang (tidak termasuk hari ini 2026-07-07).
 * Periode : 2026-06-30 s/d 2026-07-06
 *
 * Format lot receiving : {PREFIX}26Q7-{DD}{NNN}+
 *   PREFIX = kode singkat item
 *   26Q7   = tahun 2026, bulan Q7 (Juli)
 *   {DD}   = tanggal 2 digit  (30, 01, 02 … 06)
 *   {NNN}  = urutan lot dalam hari, 001–015
 *
 * Contoh : BL26Q7-30001+  = Bottle 70ml, 30 Juni 2026, lot ke-1
 *          BL26Q7-30015+  = Bottle 70ml, 30 Juni 2026, lot ke-15
 *          BL26Q7-06001+  = Bottle 70ml,  6 Juli 2026, lot ke-1
 *
 * Format lot delivery : {PREFIX}26Q7-{DD}9{NN}+
 *   {DD}9{NN} = tanggal + angka 9 sebagai separator + urutan delivery (01-dst)
 *   Contoh : BL26Q7-06901+  = Bottle 70ml, delivery 6 Juli, lot delivery ke-1
 *
 * SWA Plan bervariasi untuk simulasi pengecekan vs rencana:
 *   ON TRACK — plan_total = actual receiving qty
 *   UNDER    — plan_total > actual receiving qty (rencana belum terpenuhi)
 *   OVER     — plan_total < actual receiving qty (receiving melebihi rencana)
 *
 * Jalankan : php artisan db:seed --class=DummyInventorySeeder4
 * Reset    : jalankan ulang (cleanup otomatis via marker 26Q7- per prefix)
 */
class DummyInventorySeeder4 extends Seeder
{
    // 7 hari ke belakang, tidak termasuk hari ini (2026-07-07)
    private const DATES = [
        '2026-06-30',
        '2026-07-01',
        '2026-07-02',
        '2026-07-03',
        '2026-07-04',
        '2026-07-05',
        '2026-07-06',
    ];

    private const RCV_PER_DAY = 15; // lot 001–015 setiap hari per item

    // -------------------------------------------------------------------------
    // 10 item type (tanpa PART RETURN)
    // plan_total dibuat bervariasi agar bisa simulasi status SWA:
    //   actual receiving = 7 hari × 15 lot = 105 lot per item
    // -------------------------------------------------------------------------
    private const ITEMS = [
        [
            'part_code'  => '171739801',
            'part_name'  => 'BOTTLE,70,5300',
            'lot_prefix' => 'BL',
            'qty_box'    => 165,
            'dlv_dates'  => ['2026-07-04', '2026-07-05', '2026-07-06'],
            'dlv_count'  => 3,
            'plan_total' => 17325,  // 105 × 165 — ON TRACK
        ],
        [
            'part_code'  => '400100001',
            'part_name'  => 'NASUNO 3 CASE',
            'lot_prefix' => 'N3C',
            'qty_box'    => 112,
            'dlv_dates'  => ['2026-07-03', '2026-07-04', '2026-07-05', '2026-07-06'],
            'dlv_count'  => 2,
            'plan_total' => 14000,  // 125 × 112 — UNDER (actual 11760)
        ],
        [
            'part_code'  => '400200001',
            'part_name'  => 'CRAIG CASE L',
            'lot_prefix' => 'CCL',
            'qty_box'    => 24,
            'dlv_dates'  => ['2026-07-05', '2026-07-06'],
            'dlv_count'  => 2,
            'plan_total' => 1920,   // 80 × 24  — OVER  (actual 2520)
        ],
        [
            'part_code'  => '400200002',
            'part_name'  => 'CRAIG CASE S 4,5,6',
            'lot_prefix' => 'CCS',
            'qty_box'    => 56,
            'dlv_dates'  => ['2026-07-03', '2026-07-04', '2026-07-05', '2026-07-06'],
            'dlv_count'  => 2,
            'plan_total' => 5880,   // 105 × 56  — ON TRACK
        ],
        [
            'part_code'  => '400300001',
            'part_name'  => 'HA3P CASE S2',
            'lot_prefix' => 'HS2',
            'qty_box'    => 72,
            'dlv_dates'  => ['2026-07-05', '2026-07-06'],
            'dlv_count'  => 2,
            'plan_total' => 9072,   // 126 × 72  — UNDER (actual 7560)
        ],
        [
            'part_code'  => '300100001',
            'part_name'  => 'N3 COVER M',
            'lot_prefix' => 'NCM',
            'qty_box'    => 720,
            'dlv_dates'  => ['2026-07-05', '2026-07-06'],
            'dlv_count'  => 2,
            'plan_total' => 75600,  // 105 × 720 — ON TRACK
        ],
        [
            'part_code'  => '500100001',
            'part_name'  => 'INK BOTTLE CAP',
            'lot_prefix' => 'IBC',
            'qty_box'    => 100,
            'dlv_dates'  => ['2026-07-04', '2026-07-05', '2026-07-06'],
            'dlv_count'  => 2,
            'plan_total' => 8000,   // 80 × 100  — OVER  (actual 10500)
        ],
        [
            'part_code'  => '700100001',
            'part_name'  => 'SPOUT',
            'lot_prefix' => 'SPT',
            'qty_box'    => 200,
            'dlv_dates'  => ['2026-07-03', '2026-07-04', '2026-07-05', '2026-07-06'],
            'dlv_count'  => 2,
            'plan_total' => 21000,  // 105 × 200 — ON TRACK
        ],
        [
            'part_code'  => '600100001',
            'part_name'  => 'HAMANA GLEE',
            'lot_prefix' => 'HGL',
            'qty_box'    => 40,
            'dlv_dates'  => [],
            'dlv_count'  => 0,
            'plan_total' => 5600,   // 140 × 40  — UNDER (actual 4200)
        ],
        [
            'part_code'  => '200100002',
            'part_name'  => 'PRISM INJ',
            'lot_prefix' => 'PMI',
            'qty_box'    => 100,
            'dlv_dates'  => ['2026-07-06'],
            'dlv_count'  => 2,
            'plan_total' => 10500,  // 105 × 100 — ON TRACK
        ],
    ];

    // =========================================================================

    public function run(): void
    {
        $this->cleanup();

        $operatorId = 1;
        $createdBy  = 1;
        $planRows   = [];
        $receiving  = [];
        $delivery   = [];

        foreach (self::ITEMS as $item) {
            $prefix   = $item['lot_prefix'];
            $firstLot = null;
            $lastLot  = null;

            // -----------------------------------------------------------------
            // RECEIVING SCANS
            // Lot: {PREFIX}26Q7-{DD}{NNN}+  →  001–015 per hari
            // -----------------------------------------------------------------
            foreach (self::DATES as $date) {
                $dd = substr($date, 8, 2); // "30", "01", "02" … "06"

                for ($seq = 1; $seq <= self::RCV_PER_DAY; $seq++) {
                    $lotNo = "{$prefix}26Q7-{$dd}" . sprintf('%03d', $seq) . '+';
                    $ts    = $this->timestamp($date, $seq - 1, self::RCV_PER_DAY, 8, 16);

                    if ($firstLot === null) {
                        $firstLot = $lotNo;
                    }
                    $lastLot = $lotNo;

                    $receiving[] = [
                        'label_id'             => null,
                        'part_code'            => $item['part_code'],
                        'part_name'            => $item['part_name'],
                        'lot_no'               => $lotNo,
                        'qty_box'              => $item['qty_box'],
                        'scanned_at'           => $ts,
                        'operator_id'          => $operatorId,
                        'created_by'           => $createdBy,
                        'scan_state'           => 'receiving',
                        'delivery_at'          => null,
                        'delivery_operator_id' => null,
                        'transfer_card_no'     => null,
                        'created_at'           => $ts,
                        'updated_at'           => $ts,
                    ];
                }
            }

            // -----------------------------------------------------------------
            // DELIVERY SCANS
            // Lot: {PREFIX}26Q7-{DD}9{NN}+  →  per tanggal delivery, seq 01-dst
            // Angka "9" memisahkan tanggal dari nomor urut delivery
            // -----------------------------------------------------------------
            foreach ($item['dlv_dates'] as $dlvDate) {
                $dd = substr($dlvDate, 8, 2);
                $tc = 'TC' . str_replace('-', '', $dlvDate) . strtoupper($prefix);

                for ($seq = 1; $seq <= $item['dlv_count']; $seq++) {
                    $lotNo = "{$prefix}26Q7-{$dd}9" . sprintf('%02d', $seq) . '+';
                    $ts    = $this->timestamp($dlvDate, $seq - 1, $item['dlv_count'], 10, 17);

                    $delivery[] = [
                        'label_id'             => null,
                        'part_code'            => $item['part_code'],
                        'part_name'            => $item['part_name'],
                        'lot_no'               => $lotNo,
                        'qty_box'              => $item['qty_box'],
                        'scanned_at'           => $ts,
                        'operator_id'          => $operatorId,
                        'created_by'           => $createdBy,
                        'delivery_at'          => $ts,
                        'delivery_operator_id' => $operatorId,
                        'transfer_card_no'     => $tc . sprintf('%02d', $seq),
                        'created_at'           => $ts,
                        'updated_at'           => $ts,
                    ];
                }
            }

            // -----------------------------------------------------------------
            // SWA PLAN — dibuat sebelum periode dimulai (2026-06-29)
            // Range : dari lot pertama s/d lot terakhir receiving
            // -----------------------------------------------------------------
            $planRows[] = [
                'part_code'    => $item['part_code'],
                'part_name'    => $item['part_name'],
                'start_lot_no' => $firstLot ?? "{$prefix}26Q7-30001+",
                'end_lot_no'   => $lastLot  ?? "{$prefix}26Q7-06015+",
                'qty_box'      => $item['qty_box'],
                'total_plan'   => $item['plan_total'],
                'created_by'   => $createdBy,
                'created_at'   => '2026-06-29 07:00:00',
                'updated_at'   => '2026-06-29 07:00:00',
            ];
        }

        DB::table('fg_swa_plans')->insert($planRows);
        DB::table('fg_receiving_scans')->insert($receiving);
        DB::table('fg_delivery_scans')->insert($delivery);

        // ---------- summary ----------
        $this->command->newLine();
        $this->command->info('=== DummyInventorySeeder4 selesai ===');
        $this->command->info(sprintf('fg_swa_plans       : %d records', count($planRows)));
        $this->command->info(sprintf('fg_receiving_scans : %d records', count($receiving)));
        $this->command->info(sprintf('fg_delivery_scans  : %d records', count($delivery)));
        $this->command->newLine();
        $this->command->line(sprintf(
            '  %-22s  %4s  %4s  %9s  %9s  %8s  %s',
            'ITEM', 'RCV', 'DLV', 'RCV-QTY', 'PLAN', 'SELISIH', 'STATUS'
        ));
        $this->command->line('  ' . str_repeat('-', 95));

        foreach (self::ITEMS as $item) {
            $rcvRows = collect($receiving)->where('part_code', $item['part_code']);
            $dlvRows = collect($delivery)->where('part_code', $item['part_code']);
            $rcvQty  = $rcvRows->sum('qty_box');
            $selisih = $rcvQty - $item['plan_total'];

            if ($selisih === 0) {
                $status = 'ON TRACK';
            } elseif ($selisih > 0) {
                $status = 'OVER  (+' . $selisih . ')';
            } else {
                $status = 'UNDER (' . $selisih . ')';
            }

            $this->command->line(sprintf(
                '  %-22s  %4d  %4d  %9d  %9d  %8d  %s',
                $item['part_name'],
                $rcvRows->count(),
                $dlvRows->count(),
                $rcvQty,
                $item['plan_total'],
                $selisih,
                $status
            ));
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function timestamp(string $date, int $idx, int $total, int $startH, int $endH): string
    {
        $total  = max($total, 1);
        $spanM  = ($endH - $startH) * 60;
        $minOff = (int) round(($idx / $total) * $spanM);
        $h      = $startH + (int) floor($minOff / 60);
        $m      = $minOff % 60;
        $s      = ($idx * 17 + 3) % 60;
        return $date . ' ' . sprintf('%02d:%02d:%02d', min($h, $endH), $m, $s);
    }

    private function cleanup(): void
    {
        $prefixes = array_column(self::ITEMS, 'lot_prefix');
        $codes    = array_column(self::ITEMS, 'part_code');

        foreach ($prefixes as $pfx) {
            DB::table('fg_receiving_scans')->where('lot_no', 'like', "{$pfx}26Q7-%")->delete();
            DB::table('fg_delivery_scans')->where('lot_no', 'like', "{$pfx}26Q7-%")->delete();
        }

        // Hanya hapus SWA plan Q7 — plan Q6 dari Seeder3 tetap aman
        DB::table('fg_swa_plans')
            ->whereIn('part_code', $codes)
            ->where('start_lot_no', 'like', '%26Q7%')
            ->delete();

        $this->command->line('Cleanup selesai.');
    }
}
