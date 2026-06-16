<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'permissions',
        'phone',
        'failed_login_attempts',
        'locked_until',
        'last_activity_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'last_activity_at' => 'datetime',
            'failed_login_attempts' => 'integer',
            'permissions' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAuditor(): bool
    {
        return $this->role === 'auditor';
    }

    public function isGovernmentOfficial(): bool
    {
        return $this->role === 'government_official';
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function lockoutMinutesRemaining(): int
    {
        if (!$this->isLocked()) {
            return 0;
        }
        return (int) now()->diffInMinutes($this->locked_until, false);
    }
}
