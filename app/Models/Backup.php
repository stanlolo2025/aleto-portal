<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = [
        'filename',
        'file_path',
        'file_size_bytes',
        'status',
        'integrity_hash',
        'integrity_verified',
        'started_at',
        'completed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'integrity_verified' => 'boolean',
            'file_size_bytes' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCorrupted(): bool
    {
        return $this->status === 'corrupted';
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
