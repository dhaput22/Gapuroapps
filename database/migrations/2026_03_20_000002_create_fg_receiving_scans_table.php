<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fg_receiving_scans', function (Blueprint $table) {
            $table->id();
            $table->string('label_id', 150)->nullable();
            $table->string('part_code', 100);
            $table->string('part_name', 200)->nullable();
            $table->string('lot_no', 100);
            $table->unsignedInteger('qty_box')->default(0);
            $table->timestamp('scanned_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['part_code', 'lot_no'], 'fg_receiving_scan_lot_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fg_receiving_scans');
    }
};
