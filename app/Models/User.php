<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

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
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_USER => 'User',
        ];
    }

    public static function normalAdminRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_USER,
        ];
    }

    public static function normalAdminRoleLabels(): array
    {
        return self::roleLabels();
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleLabels()[$this->role] ?? ucfirst(str_replace('_', ' ', (string) $this->role));
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array((string) $this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN]);
    }

    public function canManageWarehouseData(): bool
    {
        return $this->isAdmin();
    }
}
