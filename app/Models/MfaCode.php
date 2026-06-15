<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class MfaCode extends Model
{
    protected $fillable = [
        'user_id',
        'code_hash',
        'resend_count',
        'expires_at',
        'used',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
            'resend_count' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(string $code): bool
    {
        return !$this->used && !$this->isExpired() && Hash::check($code, $this->code_hash);
    }

    public function markUsed(): void
    {
        $this->update(['used' => true]);
    }

    public function canResend(): bool
    {
        return $this->resend_count < 3;
    }
}
