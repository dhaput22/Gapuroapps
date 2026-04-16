<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $hasSuperAdmin = DB::table('users')
            ->where('role', User::ROLE_SUPER_ADMIN)
            ->exists();

        if ($hasSuperAdmin) {
            return;
        }

        $username = (string) env('SUPER_ADMIN_USERNAME', 'superadmin');
        $name = (string) env('SUPER_ADMIN_NAME', 'Super Admin');
        $password = (string) env('SUPER_ADMIN_PASSWORD', 'SuperAdmin@123');

        $existingSameUsername = DB::table('users')
            ->where('username', $username)
            ->exists();

        if ($existingSameUsername) {
            return;
        }

        DB::table('users')->insert([
            'name' => $name,
            'username' => $username,
            'role' => User::ROLE_SUPER_ADMIN,
            'status' => 'active',
            'password' => Hash::make($password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Tidak menghapus akun super admin secara otomatis untuk menghindari lockout.
    }
};
