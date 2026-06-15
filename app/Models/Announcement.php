<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'type', 'is_active', 'expires_at', 'created_by'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'expires_at' => 'date'];
    }

    public function createdByUser() { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        });
    }
}
