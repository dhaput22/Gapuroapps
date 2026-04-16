<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_STAFF = 'staff';
    public const ROLE_SUPERVISOR = 'supervisor';
    public const ROLE_LEADER = 'leader';

    protected $fillable = [
        'name',
        'username',
        'role',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public static function roleLabels(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_LEADER => 'Leader',
        ];
    }

    public static function normalAdminRoles(): array
    {
        return [
            self::ROLE_STAFF,
            self::ROLE_SUPERVISOR,
            self::ROLE_LEADER,
        ];
    }

    public static function normalAdminRoleLabels(): array
    {
        $labels = self::roleLabels();

        return array_intersect_key($labels, array_flip(self::normalAdminRoles()));
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleLabels()[$this->role] ?? ucfirst(str_replace('_', ' ', (string) $this->role));
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array((string) $this->role, $roles, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasAnyRole([self::ROLE_SUPER_ADMIN]);
    }

    public function canManageWarehouseData(): bool
    {
        return $this->hasAnyRole([
            self::ROLE_SUPER_ADMIN,
            self::ROLE_STAFF,
            self::ROLE_SUPERVISOR,
        ]);
    }
}
