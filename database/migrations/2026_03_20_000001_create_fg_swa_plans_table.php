<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fg_swa_plans', function (Blueprint $table) {
            $table->id();
            $table->string('part_code', 100);
            $table->string('part_name', 200);
            $table->string('start_lot_no', 100);
            $table->string('end_lot_no', 100);
            $table->unsignedInteger('qty_box');
            $table->unsignedInteger('total_plan');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['part_code', 'start_lot_no', 'end_lot_no'], 'fg_swa_plan_range_idx');
            $table->unique(['part_code', 'start_lot_no', 'end_lot_no'], 'fg_swa_plan_unique_range');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fg_swa_plans');
    }
};
