<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert all old roles to new admin/user roles
        // super_admin, staff, supervisor → admin
        // leader → user
        DB::table('users')
            ->whereIn('role', ['super_admin', 'staff', 'supervisor'])
            ->update(['role' => 'admin']);

        DB::table('users')
            ->where('role', 'leader')
            ->update(['role' => 'user']);
    }

    public function down(): void
    {
        // No way to restore original roles precisely; set all to leader as fallback
        DB::table('users')->update(['role' => 'leader']);
    }
};
