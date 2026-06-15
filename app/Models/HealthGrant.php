<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthGrant extends Model
{
    protected $fillable = [
        'grant_id', 'villager_record_id', 'grant_type', 'amount',
        'payment_method', 'approval_status', 'approved_by', 'approved_at',
        'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'approved_at' => 'datetime'];
    }

    public function grant() { return $this->belongsTo(Grant::class); }
    public function villagerRecord() { return $this->belongsTo(VillagerRecord::class); }
    public function approvedByUser() { return $this->belongsTo(User::class, 'approved_by'); }
    public function createdByUser() { return $this->belongsTo(User::class, 'created_by'); }
}
