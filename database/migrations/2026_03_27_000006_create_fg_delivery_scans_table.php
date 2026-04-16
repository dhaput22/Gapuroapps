<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fg_delivery_scans', function (Blueprint $table) {
            $table->id();
            $table->string('label_id', 150)->nullable();
            $table->string('part_code', 100);
            $table->string('part_name', 200)->nullable();
            $table->string('lot_no', 100);
            $table->unsignedInteger('qty_box')->default(0);
            $table->timestamp('scanned_at')->nullable();
            $table->foreignId('operator_id')->nullable()->constrained('operators')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('delivery_at')->nullable();
            $table->foreignId('delivery_operator_id')->nullable()->constrained('operators')->nullOnDelete();
            $table->string('transfer_card_no', 100)->nullable();
            $table->timestamps();

            $table->index(['part_code', 'lot_no'], 'fg_delivery_scan_lot_idx');
            $table->index(['delivery_at'], 'fg_delivery_scan_date_idx');
        });

        if (Schema::hasTable('fg_receiving_scans') && Schema::hasColumn('fg_receiving_scans', 'scan_state')) {
            $deliveryRows = DB::table('fg_receiving_scans')
                ->where('scan_state', 'delivery')
                ->get();

            foreach ($deliveryRows as $row) {
                DB::table('fg_delivery_scans')->insert([
                    'label_id' => $row->label_id,
                    'part_code' => $row->part_code,
                    'part_name' => $row->part_name,
                    'lot_no' => $row->lot_no,
                    'qty_box' => $row->qty_box,
                    'scanned_at' => $row->scanned_at,
                    'operator_id' => $row->operator_id,
                    'created_by' => $row->created_by,
                    'delivery_at' => $row->delivery_at,
                    'delivery_operator_id' => $row->delivery_operator_id,
                    'transfer_card_no' => $row->transfer_card_no,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            DB::table('fg_receiving_scans')->where('scan_state', 'delivery')->delete();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fg_delivery_scans');
    }
};
