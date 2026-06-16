<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed dummy data untuk 4 item selama 7 hari terakhir.
 * Lot number menggunakan format PREFIX26Q2-NNNNN+ agar sesuai validasi range di controller.
 * Semua lot (receiving + delivery) berada dalam satu range rencana SWA.
 *
 * Jalankan: php artisan db:seed --class=DummyInventorySeeder
 * Reset:    jalankan lagi (cleanup otomatis sebelum insert)
 */
class DummyInventorySeeder extends Seeder
{
    // Prefiks lot yang dipakai seeder ini — digunakan saat cleanup
    private const LOT_PREFIXES = ['BL26Q2-', 'CL26Q2-', 'HS26Q2-', 'HM26Q2-'];

    // -------------------------------------------------------------------------
    // Konfigurasi item
    // part_name HARUS cocok dengan aliases/label di config/fg_storage.php
    // -------------------------------------------------------------------------
    private const ITEMS = [
        [
            'part_code'  => '171739801',
            'part_name'  => 'BOTTLE,70,5300',
            'lot_prefix' => 'BL26Q2-',
            'lot_start'  => 21001,
            'qty_box'    => 165,
            'plan_slots' => 60,   // jumlah lot yang dicakup plan (harus >= total receiving+delivery)
        ],
        [
            'part_code'  => 'CRAIGCASEL01',
            'part_name'  => 'CRAIG CASE L',
            'lot_prefix' => 'CL26Q2-',
            'lot_start'  => 31001,
            'qty_box'    => 24,
            'plan_slots' => 35,
        ],
        [
            'part_code'  => 'HA3PCASES201',
            'part_name'  => 'HA3P CASE S2',
            'lot_prefix' => 'HS26Q2-',
            'lot_start'  => 41001,
            'qty_box'    => 172,
            'plan_slots' => 30,
        ],
        [
            'part_code'  => 'HA3PCASES2M1',
            'part_name'  => 'HA3P CASE S2 / M',
            'lot_prefix' => 'HM26Q2-',
            'lot_start'  => 51001,
            'qty_box'    => 172,
            'plan_slots' => 28,
        ],
    ];

    // Jumlah scan receiving per hari, per item [Bottle, CraigL, HA3PS2, HA3PS2M]
    private const RECEIVING_PLAN = [
        '2026-06-05' => [5, 3, 3, 2],
        '2026-06-06' => [6, 4, 3, 3],
        '2026-06-07' => [4, 3, 2, 2],
        '2026-06-08' => [5, 3, 3, 3],
        '2026-06-09' => [6, 4, 3, 2],
        '2026-06-10' => [5, 3, 3, 3],
        '2026-06-11' => [4, 2, 2, 2],
    ];

    // Jumlah scan delivery per hari, per item [Bottle, CraigL, HA3PS2, HA3PS2M]
    private const DELIVERY_PLAN = [
        '2026-06-06' => [2, 1, 1, 1],
        '2026-06-08' => [3, 1, 2, 1],
        '2026-06-09' => [2, 1, 1, 0],
        '2026-06-10' => [3, 2, 1, 2],
        '2026-06-11' => [2, 1, 1, 1],
    ];

    public function run(): void
    {
        $this->cleanup();

        $operatorId = 1;
        $createdBy  = 1;
        $planRows   = [];
        $receiving  = [];
        $delivery   = [];

        foreach (self::ITEMS as $idx => $item) {
            // ---------------------------------------------------------------
            // 1. Kumpulkan daftar lot + tanggal untuk receiving dan delivery
            // ---------------------------------------------------------------
            $receivingAssignments = $this->buildAssignments(self::RECEIVING_PLAN, $idx);
            $deliveryAssignments  = $this->buildAssignments(self::DELIVERY_PLAN, $idx);

            $totalLots = count($receivingAssignments) + count($deliveryAssignments);

            // Pastikan plan_slots mencukupi
            if ($item['plan_slots'] < $totalLots) {
                $this->command->warn("plan_slots untuk {$item['part_name']} ({$item['plan_slots']}) < total lot ({$totalLots}). Disesuaikan otomatis.");
                $item['plan_slots'] = $totalLots + 5;
            }

            $startLotNum = $item['lot_start'];
            $endLotNum   = $startLotNum + $item['plan_slots'] - 1;
            $startLotNo  = $item['lot_prefix'] . $startLotNum . '+';
            $endLotNo    = $item['lot_prefix'] . $endLotNum . '+';
            $totalPlan   = $item['plan_slots'] * $item['qty_box'];

            // ---------------------------------------------------------------
            // 2. SWA Plan
            // ---------------------------------------------------------------
            $planRows[] = [
                'part_code'    => $item['part_code'],
                'part_name'    => $item['part_name'],
                'start_lot_no' => $startLotNo,
                'end_lot_no'   => $endLotNo,
                'qty_box'      => $item['qty_box'],
                'total_plan'   => $totalPlan,
                'created_by'   => $createdBy,
                'created_at'   => '2026-06-04 07:30:00',
                'updated_at'   => '2026-06-04 07:30:00',
            ];

            // ---------------------------------------------------------------
            // 3. Receiving scans
            //    Lot number: startLotNum, startLotNum+1, ... (urut per hari)
            // ---------------------------------------------------------------
            $lotNum = $startLotNum;
            foreach ($receivingAssignments as $k => [$date, $scanIndexInDay, $totalInDay]) {
                $ts = $this->makeTimestamp($date, $scanIndexInDay, $totalInDay, 8, 16);

                $receiving[] = [
                    'label_id'             => null,
                    'part_code'            => $item['part_code'],
                    'part_name'            => $item['part_name'],
                    'lot_no'               => $item['lot_prefix'] . $lotNum . '+',
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
                $lotNum++;
            }

            // ---------------------------------------------------------------
            // 4. Delivery scans
            //    Lot number: melanjutkan setelah receiving, tetap dalam range plan
            //    Mensimulasikan barang yang sudah diterima lalu dikirim keluar
            // ---------------------------------------------------------------
            foreach ($deliveryAssignments as $k => [$date, $scanIndexInDay, $totalInDay]) {
                $ts  = $this->makeTimestamp($date, $scanIndexInDay, $totalInDay, 10, 17);
                $tc  = 'TC-' . str_replace('-', '', $date) . '-' . strtoupper(substr($item['part_code'], 0, 6));

                $delivery[] = [
                    'label_id'             => null,
                    'part_code'            => $item['part_code'],
                    'part_name'            => $item['part_name'],
                    'lot_no'               => $item['lot_prefix'] . $lotNum . '+',
                    'qty_box'              => $item['qty_box'],
                    'scanned_at'           => $ts,
                    'operator_id'          => $operatorId,
                    'created_by'           => $createdBy,
                    'delivery_at'          => $ts,
                    'delivery_operator_id' => $operatorId,
                    'transfer_card_no'     => $tc,
                    'created_at'           => $ts,
                    'updated_at'           => $ts,
                ];
                $lotNum++;
            }
        }

        DB::table('fg_swa_plans')->insert($planRows);
        DB::table('fg_receiving_scans')->insert($receiving);
        DB::table('fg_delivery_scans')->insert($delivery);

        $this->command->info('fg_swa_plans   : ' . count($planRows) . ' records');
        $this->command->info('fg_receiving_scans : ' . count($receiving) . ' records');
        $this->command->info('fg_delivery_scans  : ' . count($delivery) . ' records');
        $this->command->newLine();

        // Ringkasan per item
        foreach (self::ITEMS as $idx => $item) {
            $rcv = collect($receiving)->where('part_code', $item['part_code'])->count();
            $dlv = collect($delivery)->where('part_code', $item['part_code'])->count();
            $stk = $rcv - $dlv;
            $this->command->line("  [{$item['part_name']}]  receive={$rcv}  delivery={$dlv}  stock={$stk} (di gudang)");
        }
    }

    // -------------------------------------------------------------------------
    // Helper: buat daftar [date, scanIndexInDay, totalInDay]
    // -------------------------------------------------------------------------
    private function buildAssignments(array $plan, int $itemIdx): array
    {
        $assignments = [];
        foreach ($plan as $date => $counts) {
            $count = (int) ($counts[$itemIdx] ?? 0);
            for ($i = 0; $i < $count; $i++) {
                $assignments[] = [$date, $i, $count];
            }
        }
        return $assignments;
    }

    // -------------------------------------------------------------------------
    // Helper: buat timestamp realistis dalam rentang jam tertentu
    // -------------------------------------------------------------------------
    private function makeTimestamp(string $date, int $idx, int $total, int $startHour, int $endHour): string
    {
        $total    = max($total, 1);
        $span     = ($endHour - $startHour) * 60; // dalam menit
        $minute   = (int) round(($idx / $total) * $span);
        $hour     = $startHour + (int) floor($minute / 60);
        $min      = $minute % 60;
        $sec      = ($idx * 17 + 3) % 60;
        return "{$date} " . sprintf('%02d:%02d:%02d', min($hour, $endHour), $min, $sec);
    }

    // -------------------------------------------------------------------------
    // Cleanup: hapus data seeder sebelumnya berdasarkan prefix lot
    // -------------------------------------------------------------------------
    private function cleanup(): void
    {
        foreach (self::LOT_PREFIXES as $prefix) {
            DB::table('fg_receiving_scans')->where('lot_no', 'like', $prefix . '%')->delete();
            DB::table('fg_delivery_scans')->where('lot_no', 'like', $prefix . '%')->delete();
        }

        DB::table('fg_swa_plans')->where('start_lot_no', 'like', '%26Q2%')->delete();

        $this->command->line('Cleanup selesai.');
    }
}
