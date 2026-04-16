<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fg_receiving_scans', function (Blueprint $table) {
            $table->string('scan_state', 20)->default('receiving')->after('operator_id');
            $table->timestamp('delivery_at')->nullable()->after('scan_state');
            $table->foreignId('delivery_operator_id')->nullable()->after('delivery_at')->constrained('operators')->nullOnDelete();
            $table->string('transfer_card_no', 100)->nullable()->after('delivery_operator_id');

            $table->index(['scan_state', 'delivery_at'], 'fg_receiving_scan_delivery_idx');
        });
    }

    public function down(): void
    {
        Schema::table('fg_receiving_scans', function (Blueprint $table) {
            $table->dropIndex('fg_receiving_scan_delivery_idx');
            $table->dropConstrainedForeignId('delivery_operator_id');
            $table->dropColumn(['scan_state', 'delivery_at', 'transfer_card_no']);
        });
    }
};
