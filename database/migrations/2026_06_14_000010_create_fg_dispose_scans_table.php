<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fg_dispose_scans', function (Blueprint $table) {
            $table->id();
            $table->string('label_id', 150)->nullable();
            $table->string('part_code', 100);
            $table->string('part_name', 200)->nullable();
            $table->string('lot_no', 100);
            $table->unsignedInteger('qty_box')->default(0);
            $table->timestamp('scanned_at')->nullable();
            $table->foreignId('operator_id')->nullable()->constrained('operators')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dispose_at')->nullable();
            $table->foreignId('dispose_operator_id')->nullable()->constrained('operators')->nullOnDelete();
            $table->string('remark', 255)->nullable();
            $table->timestamps();

            $table->index(['part_code', 'lot_no'], 'fg_dispose_scan_lot_idx');
            $table->index(['dispose_at'], 'fg_dispose_scan_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fg_dispose_scans');
    }
};
