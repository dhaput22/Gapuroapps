<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dummy data komprehensif untuk semua 29 item type.
 * Rentang tanggal : satu minggu kebelakang (2026-06-16 s/d 2026-06-22)
 *
 * Format lot number  : {PREFIX}26Q6-{DD}{NNN}+
 *   PREFIX  = kode singkat item (unik per item, 2-5 huruf)
 *   26      = tahun 2026
 *   Q6      = bulan ke-6 (Juni)
 *   -{DD}   = hari dalam bulan, 2 digit (16 – 22)
 *   {NNN}   = nomor urut lot dalam hari tersebut (001, 002 …)
 *   +       = akhiran standar
 *
 * Contoh : BL26Q6-16001+  = Bottle 70ml, 16 Juni 2026, lot ke-1
 *          N3C26Q6-20003+ = NASUNO 3 CASE, 20 Juni 2026, lot ke-3
 *
 * Delivery lot : hari pengiriman + seq mulai 901 untuk menghindari tabrakan
 *   Contoh : BL26Q6-20901+  = Bottle 70ml, dikirim 20 Juni, lot delivery ke-1
 *
 * Jalankan : php artisan db:seed --class=DummyInventorySeeder3
 * Reset    : jalankan ulang (cleanup otomatis via marker 26Q6-)
 */
class DummyInventorySeeder3 extends Seeder
{
    // 7 hari sebelum hari ini (2026-06-23)
    private const DATES = [
        '2026-06-16',
        '2026-06-17',
        '2026-06-18',
        '2026-06-19',
        '2026-06-20',
        '2026-06-21',
        '2026-06-22',
    ];

    // -------------------------------------------------------------------------
    // ITEM DEFINITIONS
    // part_name HARUS cocok aliases/label di config/fg_storage.php
    // part_code: integer string (sesuai permintaan)
    // qty_box  : qty per box sesuai ketentuan
    // rcv_per_day : jumlah lot receiving per hari (3-4 → total 21-28)
    // dlv_dates   : tanggal delivery (subset dari DATES, null = tidak ada delivery)
    // dlv_count   : jumlah lot delivery per tanggal tersebut
    // -------------------------------------------------------------------------
    private const ITEMS = [
        // ── BOTTLE ──────────────────────────────────────────────────────────
        [
            'part_code'   => '100100001',
            'part_name'   => 'BOTTLE,20',
            'lot_prefix'  => 'B20',
            'qty_box'     => 120,
            'rcv_per_day' => 3,   // 3 × 7 = 21 records
            'dlv_dates'   => ['2026-06-20', '2026-06-21'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '100100002',
            'part_name'   => 'BOTTLE,40',
            'lot_prefix'  => 'B40',
            'qty_box'     => 120,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-20', '2026-06-21'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '171739801',   // part code asli dari DB
            'part_name'   => 'BOTTLE,70,5300',
            'lot_prefix'  => 'BL',
            'qty_box'     => 165,
            'rcv_per_day' => 4,   // 4 × 7 = 28 records
            'dlv_dates'   => ['2026-06-19', '2026-06-20', '2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],

        // ── PRISM ───────────────────────────────────────────────────────────
        [
            'part_code'   => '200100002',
            'part_name'   => 'PRISM INJ',
            'lot_prefix'  => 'PMI',
            'qty_box'     => 100,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '200100003',
            'part_name'   => 'SAKURA',
            'lot_prefix'  => 'SKR',
            'qty_box'     => 56,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],
        [
            'part_code'   => '200100004',
            'part_name'   => 'PRISM COATING',
            'lot_prefix'  => 'PCO',
            'qty_box'     => 30,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],

        // ── N3 COVER ────────────────────────────────────────────────────────
        [
            'part_code'   => '300100001',
            'part_name'   => 'N3 COVER M',
            'lot_prefix'  => 'NCM',
            'qty_box'     => 720,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-22'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '300100002',
            'part_name'   => 'N3 COVER',
            'lot_prefix'  => 'NCV',
            'qty_box'     => 720,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-22'],
            'dlv_count'   => 2,
        ],

        // ── NASUNO / N3 CASE ────────────────────────────────────────────────
        [
            'part_code'   => '400100001',
            'part_name'   => 'NASUNO 3 CASE',
            'lot_prefix'  => 'N3C',
            'qty_box'     => 112,
            'rcv_per_day' => 4,
            'dlv_dates'   => ['2026-06-18', '2026-06-19', '2026-06-20', '2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '400100002',
            'part_name'   => 'N3 CASE M1, M2',
            'lot_prefix'  => 'N3M',
            'qty_box'     => 112,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-20', '2026-06-21'],
            'dlv_count'   => 2,
        ],

        // ── CRAIG CASE ──────────────────────────────────────────────────────
        [
            'part_code'   => '400200001',
            'part_name'   => 'CRAIG CASE L',
            'lot_prefix'  => 'CCL',
            'qty_box'     => 24,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '400200002',
            'part_name'   => 'CRAIG CASE S 4,5,6',
            'lot_prefix'  => 'CCS',
            'qty_box'     => 56,
            'rcv_per_day' => 4,
            'dlv_dates'   => ['2026-06-18', '2026-06-19', '2026-06-20', '2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],

        // ── HA3P CASE ───────────────────────────────────────────────────────
        [
            'part_code'   => '400300001',
            'part_name'   => 'HA3P CASE S2',
            'lot_prefix'  => 'HS2',
            'qty_box'     => 72,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-20', '2026-06-22'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '400300002',
            'part_name'   => 'HA3P CASE S2 / M',
            'lot_prefix'  => 'HSM',
            'qty_box'     => 72,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-20', '2026-06-22'],
            'dlv_count'   => 2,
        ],

        // ── CAP ─────────────────────────────────────────────────────────────
        [
            'part_code'   => '500100001',
            'part_name'   => 'INK BOTTLE CAP',
            'lot_prefix'  => 'IBC',
            'qty_box'     => 100,
            'rcv_per_day' => 4,
            'dlv_dates'   => ['2026-06-19', '2026-06-20', '2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '500100002',
            'part_name'   => 'TOP CAP',
            'lot_prefix'  => 'TCP',
            'qty_box'     => 100,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],

        // ── HAMANA ──────────────────────────────────────────────────────────
        [
            'part_code'   => '600100001',
            'part_name'   => 'HAMANA GLEE',
            'lot_prefix'  => 'HGL',
            'qty_box'     => 40,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],
        [
            'part_code'   => '600100002',
            'part_name'   => 'HAMANA GROW',
            'lot_prefix'  => 'HGR',
            'qty_box'     => 40,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],

        // ── SPOUT / MASHU ───────────────────────────────────────────────────
        [
            'part_code'   => '700100001',
            'part_name'   => 'SPOUT',
            'lot_prefix'  => 'SPT',
            'qty_box'     => 200,
            'rcv_per_day' => 4,
            'dlv_dates'   => ['2026-06-18', '2026-06-19', '2026-06-20', '2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '700100002',
            'part_name'   => 'MASHU',
            'lot_prefix'  => 'MSH',
            'qty_box'     => 80,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-21', '2026-06-22'],
            'dlv_count'   => 2,
        ],

        // ── S15 / A3 / FB / ADF / LG ────────────────────────────────────────
        [
            'part_code'   => '800100001',
            'part_name'   => 'S15',
            'lot_prefix'  => 'S15',
            'qty_box'     => 50,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],
        [
            'part_code'   => '800100002',
            'part_name'   => 'A3',
            'lot_prefix'  => 'AA3',
            'qty_box'     => 50,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],
        [
            'part_code'   => '800100003',
            'part_name'   => 'FB',
            'lot_prefix'  => 'FFB',
            'qty_box'     => 40,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],
        [
            'part_code'   => '800100004',
            'part_name'   => 'ADF',
            'lot_prefix'  => 'ADF',
            'qty_box'     => 40,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],
        [
            'part_code'   => '800100005',
            'part_name'   => 'LG',
            'lot_prefix'  => 'LLG',
            'qty_box'     => 30,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],

        // ── COVER / COATING ─────────────────────────────────────────────────
        [
            'part_code'   => '900100001',
            'part_name'   => 'CRAIG COVER L',
            'lot_prefix'  => 'CVL',
            'qty_box'     => 100,
            'rcv_per_day' => 3,
            'dlv_dates'   => ['2026-06-22'],
            'dlv_count'   => 2,
        ],
        [
            'part_code'   => '900100002',
            'part_name'   => 'NASUNO CASE B',
            'lot_prefix'  => 'NCB',
            'qty_box'     => 80,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],
        [
            'part_code'   => '900100003',
            'part_name'   => 'COVER S, M',
            'lot_prefix'  => 'CSM',
            'qty_box'     => 40,
            'rcv_per_day' => 3,
            'dlv_dates'   => [],
            'dlv_count'   => 0,
        ],
    ];

    // =========================================================================

    public function run(): void
    {
        $this->cleanup();

        $operatorId = 1;
        $createdBy  = 1;

        $planRows  = [];
        $receiving = [];
        $delivery  = [];

        foreach (self::ITEMS as $item) {
            $prefix = $item['lot_prefix'];
            $firstLot = null;
            $lastLot  = null;

            // -----------------------------------------------------------------
            // RECEIVING SCANS
            // Lot: {PREFIX}26Q6-{DD}{NNN}+   e.g. BL26Q6-16001+
            // -----------------------------------------------------------------
            foreach (self::DATES as $date) {
                $dd = substr($date, 8, 2); // "16", "17", …

                for ($seq = 1; $seq <= $item['rcv_per_day']; $seq++) {
                    $lotNo = "{$prefix}26Q6-{$dd}" . sprintf('%03d', $seq) . '+';
                    $ts    = $this->timestamp($date, $seq - 1, $item['rcv_per_day'], 8, 16);

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
            // Lot: {PREFIX}26Q6-{DD}9{NN}+   seq 901, 902 … (terpisah dari receiving)
            // Mensimulasikan barang yg sudah diterima lalu dikirim keluar gudang
            // -----------------------------------------------------------------
            foreach ($item['dlv_dates'] as $dlvDate) {
                $dd  = substr($dlvDate, 8, 2);
                $tc  = 'TC' . str_replace('-', '', $dlvDate) . strtoupper($prefix);

                for ($seq = 1; $seq <= $item['dlv_count']; $seq++) {
                    $lotNo = "{$prefix}26Q6-{$dd}9" . sprintf('%02d', $seq) . '+';
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
            // SWA PLAN
            // Satu plan per item mencakup seluruh range lot dalam seeder ini
            // -----------------------------------------------------------------
            $totalLots  = count(self::DATES) * $item['rcv_per_day']
                        + count($item['dlv_dates']) * $item['dlv_count'];
            $planRows[] = [
                'part_code'    => $item['part_code'],
                'part_name'    => $item['part_name'],
                'start_lot_no' => $firstLot ?? "{$prefix}26Q6-16001+",
                'end_lot_no'   => $lastLot  ?? "{$prefix}26Q6-22004+",
                'qty_box'      => $item['qty_box'],
                'total_plan'   => $totalLots * $item['qty_box'],
                'created_by'   => $createdBy,
                'created_at'   => '2026-06-15 07:00:00',
                'updated_at'   => '2026-06-15 07:00:00',
            ];
        }

        DB::table('fg_swa_plans')->insert($planRows);
        DB::table('fg_receiving_scans')->insert($receiving);
        DB::table('fg_delivery_scans')->insert($delivery);

        // ---------- summary ----------
        $this->command->newLine();
        $this->command->info('=== DummyInventorySeeder3 selesai ===');
        $this->command->info(sprintf('fg_swa_plans       : %d records', count($planRows)));
        $this->command->info(sprintf('fg_receiving_scans : %d records', count($receiving)));
        $this->command->info(sprintf('fg_delivery_scans  : %d records', count($delivery)));
        $this->command->newLine();
        $this->command->line(sprintf('  %-22s %-20s  %5s  %5s  %6s  %6s  %6s',
            'ITEM', 'PART CODE', 'RCV', 'DLV', 'RCV-QTY', 'DLV-QTY', 'STOCK'));
        $this->command->line('  ' . str_repeat('-', 82));

        foreach (self::ITEMS as $item) {
            $rcvRows = collect($receiving)->where('part_code', $item['part_code']);
            $dlvRows = collect($delivery)->where('part_code', $item['part_code']);
            $rcvQty  = $rcvRows->sum('qty_box');
            $dlvQty  = $dlvRows->sum('qty_box');
            $this->command->line(sprintf(
                '  %-22s %-20s  %5d  %5d  %6d  %6d  %6d',
                $item['part_name'],
                $item['part_code'],
                $rcvRows->count(),
                $dlvRows->count(),
                $rcvQty,
                $dlvQty,
                $rcvQty - $dlvQty
            ));
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /** Timestamp realistis dalam rentang jam startHour–endHour */
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

    /** Hapus data seeder ini berdasarkan marker 26Q6- per prefix */
    private function cleanup(): void
    {
        $prefixes = array_column(self::ITEMS, 'lot_prefix');
        $codes    = array_column(self::ITEMS, 'part_code');

        foreach ($prefixes as $pfx) {
            DB::table('fg_receiving_scans')->where('lot_no', 'like', "{$pfx}26Q6-%")->delete();
            DB::table('fg_delivery_scans')->where('lot_no', 'like', "{$pfx}26Q6-%")->delete();
        }

        DB::table('fg_swa_plans')->whereIn('part_code', $codes)->delete();

        $this->command->line('Cleanup selesai.');
    }
}
