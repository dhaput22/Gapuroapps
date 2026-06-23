<?php

namespace Database\Seeders;

use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dummy data untuk 25 item type lainnya (selain 4 item di DummyInventorySeeder).
 * Rentang tanggal: 2026-06-13 (1 minggu lalu) s/d 2026-07-04 (2 minggu ke depan).
 *
 * Format lot: {PREFIX}-{YYMMDD}-{NNN}+
 *   PREFIX  = kode singkat item (unik per item)
 *   YYMMDD  = tanggal barang masuk/keluar (mis. 260613 = 2026-06-13)
 *   NNN     = urutan lot dalam hari tersebut (001, 002, ...)
 *
 * Jalankan : php artisan db:seed --class=DummyInventorySeeder2
 * Reset    : jalankan lagi (cleanup otomatis via prefix)
 */
class DummyInventorySeeder2 extends Seeder
{
    private const TODAY      = '2026-06-20';
    private const START_DATE = '2026-06-13';   // 1 minggu lalu
    private const END_DATE   = '2026-07-04';   // 2 minggu ke depan

    // -------------------------------------------------------------------------
    // Definisi item
    // lot_prefix : unik, dipakai untuk cleanup
    // part_name  : HARUS cocok dengan aliases/label di config/fg_storage.php
    // qty_box    : qty per box
    // receive_every : terima tiap N hari (1 = setiap hari, 2 = selang 1 hari, ...)
    // receive_count : jumlah lot per jadwal terima
    // delivery_lag  : pengiriman terjadi N hari setelah receive (null = tidak ada delivery)
    // delivery_count: lot yang dikirim per jadwal (harus <= receive_count)
    // -------------------------------------------------------------------------
    private const ITEMS = [
        // ---------- BOTTLE ----------
        [
            'lot_prefix'     => 'B20',
            'part_code'      => 'BTL20ML01',
            'part_name'      => 'BOTTLE,20',
            'qty_box'        => 150,
            'receive_every'  => 2,
            'receive_count'  => 3,
            'delivery_lag'   => 3,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'B40',
            'part_code'      => 'BTL40ML01',
            'part_name'      => 'BOTTLE,40',
            'qty_box'        => 120,
            'receive_every'  => 2,
            'receive_count'  => 3,
            'delivery_lag'   => 3,
            'delivery_count' => 2,
        ],

        // ---------- CASE / COVER ----------
        [
            'lot_prefix'     => 'N3C',
            'part_code'      => 'NASUNO3C01',
            'part_name'      => 'NASUNO 3 CASE',
            'qty_box'        => 80,
            'receive_every'  => 1,
            'receive_count'  => 4,
            'delivery_lag'   => 2,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'N3M',
            'part_code'      => 'N3CASEM1M2',
            'part_name'      => 'N3 CASE M1, M2',
            'qty_box'        => 60,
            'receive_every'  => 2,
            'receive_count'  => 3,
            'delivery_lag'   => 4,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'N3V',
            'part_code'      => 'N3COVER001',
            'part_name'      => 'N3 COVER',
            'qty_box'        => 60,
            'receive_every'  => 3,
            'receive_count'  => 3,
            'delivery_lag'   => 5,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'N3VM',
            'part_code'      => 'N3COVERM01',
            'part_name'      => 'N3 COVER M',
            'qty_box'        => 60,
            'receive_every'  => 3,
            'receive_count'  => 3,
            'delivery_lag'   => 5,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'CCS',
            'part_code'      => 'CRAIGCASS1',
            'part_name'      => 'CRAIG CASE S 4,5,6',
            'qty_box'        => 36,
            'receive_every'  => 1,
            'receive_count'  => 5,
            'delivery_lag'   => 2,
            'delivery_count' => 3,
        ],
        [
            'lot_prefix'     => 'CCL',
            'part_code'      => 'CRAIGCOVL1',
            'part_name'      => 'CRAIG COVER L',
            'qty_box'        => 100,
            'receive_every'  => 3,
            'receive_count'  => 3,
            'delivery_lag'   => 5,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'NCB',
            'part_code'      => 'NASUNOCAB1',
            'part_name'      => 'NASUNO CASE B',
            'qty_box'        => 80,
            'receive_every'  => 3,
            'receive_count'  => 3,
            'delivery_lag'   => 5,
            'delivery_count' => 2,
        ],

        // ---------- PART / KOMPONEN ----------
        [
            'lot_prefix'     => 'PMI',
            'part_code'      => 'PRISMINJC1',
            'part_name'      => 'PRISM INJ',
            'qty_box'        => 100,
            'receive_every'  => 2,
            'receive_count'  => 3,
            'delivery_lag'   => 4,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'SKR',
            'part_code'      => 'SAKURA0001',
            'part_name'      => 'SAKURA',
            'qty_box'        => 50,
            'receive_every'  => 4,
            'receive_count'  => 3,
            'delivery_lag'   => 6,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'PTR',
            'part_code'      => 'PARTRETURN',
            'part_name'      => 'PART RETURN',
            'qty_box'        => 20,
            'receive_every'  => 5,
            'receive_count'  => 2,
            'delivery_lag'   => 7,
            'delivery_count' => 1,
        ],
        [
            'lot_prefix'     => 'PCO',
            'part_code'      => 'PRISMCOAT1',
            'part_name'      => 'PRISM COATING',
            'qty_box'        => 30,
            'receive_every'  => 4,
            'receive_count'  => 2,
            'delivery_lag'   => 6,
            'delivery_count' => 1,
        ],

        // ---------- CAP / TUTUP ----------
        [
            'lot_prefix'     => 'IBC',
            'part_code'      => 'INKBTLCAP1',
            'part_name'      => 'INK BOTTLE CAP',
            'qty_box'        => 100,
            'receive_every'  => 1,
            'receive_count'  => 4,
            'delivery_lag'   => 2,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'TCP',
            'part_code'      => 'TOPCAP0001',
            'part_name'      => 'TOP CAP',
            'qty_box'        => 100,
            'receive_every'  => 2,
            'receive_count'  => 3,
            'delivery_lag'   => 3,
            'delivery_count' => 2,
        ],

        // ---------- HAMANA ----------
        [
            'lot_prefix'     => 'HGL',
            'part_code'      => 'HAMANAGLEE',
            'part_name'      => 'HAMANA GLEE',
            'qty_box'        => 40,
            'receive_every'  => 3,
            'receive_count'  => 3,
            'delivery_lag'   => 4,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'HGR',
            'part_code'      => 'HAMANAGROW',
            'part_name'      => 'HAMANA GROW',
            'qty_box'        => 40,
            'receive_every'  => 3,
            'receive_count'  => 3,
            'delivery_lag'   => 4,
            'delivery_count' => 2,
        ],

        // ---------- SPOUT / MASHU ----------
        [
            'lot_prefix'     => 'SPT',
            'part_code'      => 'SPOUT00001',
            'part_name'      => 'SPOUT',
            'qty_box'        => 200,
            'receive_every'  => 1,
            'receive_count'  => 4,
            'delivery_lag'   => 2,
            'delivery_count' => 2,
        ],
        [
            'lot_prefix'     => 'MSH',
            'part_code'      => 'MASHU00001',
            'part_name'      => 'MASHU',
            'qty_box'        => 80,
            'receive_every'  => 2,
            'receive_count'  => 3,
            'delivery_lag'   => 3,
            'delivery_count' => 2,
        ],

        // ---------- S15 / A3 / FB / ADF / LG ----------
        [
            'lot_prefix'     => 'S15',
            'part_code'      => 'S15000001',
            'part_name'      => 'S15',
            'qty_box'        => 50,
            'receive_every'  => 3,
            'receive_count'  => 2,
            'delivery_lag'   => 5,
            'delivery_count' => 1,
        ],
        [
            'lot_prefix'     => 'AA3',
            'part_code'      => 'A300000001',
            'part_name'      => 'A3',
            'qty_box'        => 50,
            'receive_every'  => 3,
            'receive_count'  => 2,
            'delivery_lag'   => 5,
            'delivery_count' => 1,
        ],
        [
            'lot_prefix'     => 'FFB',
            'part_code'      => 'FB00000001',
            'part_name'      => 'FB',
            'qty_box'        => 40,
            'receive_every'  => 4,
            'receive_count'  => 2,
            'delivery_lag'   => 6,
            'delivery_count' => 1,
        ],
        [
            'lot_prefix'     => 'ADF',
            'part_code'      => 'ADF0000001',
            'part_name'      => 'ADF',
            'qty_box'        => 40,
            'receive_every'  => 4,
            'receive_count'  => 2,
            'delivery_lag'   => 6,
            'delivery_count' => 1,
        ],
        [
            'lot_prefix'     => 'LLG',
            'part_code'      => 'LG00000001',
            'part_name'      => 'LG',
            'qty_box'        => 30,
            'receive_every'  => 5,
            'receive_count'  => 2,
            'delivery_lag'   => 7,
            'delivery_count' => 1,
        ],

        // ---------- COVER ----------
        [
            'lot_prefix'     => 'CSM',
            'part_code'      => 'COVERSM001',
            'part_name'      => 'COVER S, M',
            'qty_box'        => 40,
            'receive_every'  => 4,
            'receive_count'  => 2,
            'delivery_lag'   => 6,
            'delivery_count' => 1,
        ],
    ];

    // -------------------------------------------------------------------------

    public function run(): void
    {
        $this->cleanup();

        $operatorId = 1;
        $createdBy  = 1;

        $allDates = $this->generateDateRange(self::START_DATE, self::END_DATE);

        $planRows  = [];
        $receiving = [];
        $delivery  = [];

        foreach (self::ITEMS as $item) {
            // --- jadwal receiving: setiap N hari mulai dari START_DATE ---
            $receiveDates = [];
            foreach ($allDates as $i => $date) {
                if ($i % $item['receive_every'] === 0) {
                    $receiveDates[] = $date;
                }
            }

            // --- jadwal delivery: hanya untuk tanggal LALU + hari ini ---
            $deliveryDates = [];
            foreach ($receiveDates as $rDate) {
                $dDate = $this->addDays($rDate, $item['delivery_lag']);
                // Delivery hanya bisa terjadi <= TODAY
                if ($dDate <= self::TODAY && $dDate >= self::START_DATE) {
                    $deliveryDates[] = $dDate;
                }
            }
            $deliveryDates = array_unique($deliveryDates);

            // --- buat lot numbers per tanggal agar mudah dilacak ---
            foreach ($receiveDates as $date) {
                $dateCode = substr($date, 2, 2) . substr($date, 5, 2) . substr($date, 8, 2); // YYMMDD
                for ($seq = 1; $seq <= $item['receive_count']; $seq++) {
                    $lotNo = $item['lot_prefix'] . '-' . $dateCode . '-' . sprintf('%03d', $seq) . '+';
                    $ts    = $this->makeTimestamp($date, $seq - 1, $item['receive_count'], 8, 16);

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

            foreach ($deliveryDates as $date) {
                $dateCode = substr($date, 2, 2) . substr($date, 5, 2) . substr($date, 8, 2);
                $tcBase   = 'TC-' . str_replace('-', '', $date) . '-' . strtoupper($item['lot_prefix']);
                for ($seq = 1; $seq <= $item['delivery_count']; $seq++) {
                    $lotNo = $item['lot_prefix'] . '-D' . $dateCode . '-' . sprintf('%03d', $seq) . '+';
                    $ts    = $this->makeTimestamp($date, $seq - 1, $item['delivery_count'], 10, 17);

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
                        'transfer_card_no'     => $tcBase . sprintf('%02d', $seq),
                        'created_at'           => $ts,
                        'updated_at'           => $ts,
                    ];
                }
            }

            // --- SWA Plan: satu plan per item mencakup semua lot ---
            $totalLots = (count($receiveDates) + count($deliveryDates)) * max($item['receive_count'], $item['delivery_count']) + 5;
            $startSeq  = 1;
            $dateCode0 = substr(self::START_DATE, 2, 2) . substr(self::START_DATE, 5, 2) . substr(self::START_DATE, 8, 2);
            $dateCode1 = substr(self::END_DATE, 2, 2) . substr(self::END_DATE, 5, 2) . substr(self::END_DATE, 8, 2);

            $planRows[] = [
                'part_code'    => $item['part_code'],
                'part_name'    => $item['part_name'],
                'start_lot_no' => $item['lot_prefix'] . '-' . $dateCode0 . '-001+',
                'end_lot_no'   => $item['lot_prefix'] . '-' . $dateCode1 . '-' . sprintf('%03d', $item['receive_count']) . '+',
                'qty_box'      => $item['qty_box'],
                'total_plan'   => $totalLots * $item['qty_box'],
                'created_by'   => $createdBy,
                'created_at'   => '2026-06-12 07:00:00',
                'updated_at'   => '2026-06-12 07:00:00',
            ];
        }

        DB::table('fg_swa_plans')->insert($planRows);
        DB::table('fg_receiving_scans')->insert($receiving);
        DB::table('fg_delivery_scans')->insert($delivery);

        $this->command->info('fg_swa_plans       : ' . count($planRows) . ' records');
        $this->command->info('fg_receiving_scans : ' . count($receiving) . ' records');
        $this->command->info('fg_delivery_scans  : ' . count($delivery) . ' records');
        $this->command->newLine();

        foreach (self::ITEMS as $item) {
            $rcv     = collect($receiving)->where('part_code', $item['part_code'])->count();
            $rcvQty  = collect($receiving)->where('part_code', $item['part_code'])->sum('qty_box');
            $dlv     = collect($delivery)->where('part_code', $item['part_code'])->count();
            $dlvQty  = collect($delivery)->where('part_code', $item['part_code'])->sum('qty_box');
            $stkQty  = $rcvQty - $dlvQty;
            $this->command->line(
                sprintf('  %-22s  rcv=%2d (%5d box)  dlv=%2d (%5d box)  stock=%5d box',
                    $item['part_name'], $rcv, $rcvQty, $dlv, $dlvQty, $stkQty)
            );
        }
    }

    // -------------------------------------------------------------------------
    // Helper: hasilkan semua tanggal antara $start dan $end (inklusif)
    // -------------------------------------------------------------------------
    private function generateDateRange(string $start, string $end): array
    {
        $dates   = [];
        $current = new DateTime($start);
        $last    = new DateTime($end);

        while ($current <= $last) {
            $dates[] = $current->format('Y-m-d');
            $current->modify('+1 day');
        }

        return $dates;
    }

    // -------------------------------------------------------------------------
    // Helper: tambah N hari ke tanggal
    // -------------------------------------------------------------------------
    private function addDays(string $date, int $days): string
    {
        $dt = new DateTime($date);
        $dt->modify("+{$days} days");
        return $dt->format('Y-m-d');
    }

    // -------------------------------------------------------------------------
    // Helper: timestamp realistis dalam rentang jam
    // -------------------------------------------------------------------------
    private function makeTimestamp(string $date, int $idx, int $total, int $startHour, int $endHour): string
    {
        $total  = max($total, 1);
        $span   = ($endHour - $startHour) * 60;
        $minute = (int) round(($idx / $total) * $span);
        $hour   = $startHour + (int) floor($minute / 60);
        $min    = $minute % 60;
        $sec    = ($idx * 17 + 7) % 60;
        return $date . ' ' . sprintf('%02d:%02d:%02d', min($hour, $endHour), $min, $sec);
    }

    // -------------------------------------------------------------------------
    // Cleanup: hapus data seeder ini berdasarkan prefiks lot
    // -------------------------------------------------------------------------
    private function cleanup(): void
    {
        $prefixes = array_column(self::ITEMS, 'lot_prefix');

        foreach ($prefixes as $prefix) {
            DB::table('fg_receiving_scans')->where('lot_no', 'like', $prefix . '-%')->delete();
            DB::table('fg_delivery_scans')->where('lot_no', 'like', $prefix . '-%')->delete();
        }

        $partCodes = array_column(self::ITEMS, 'part_code');
        DB::table('fg_swa_plans')->whereIn('part_code', $partCodes)->delete();

        $this->command->line('Cleanup selesai.');
    }
}
