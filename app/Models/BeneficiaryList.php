<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryList extends Model
{
    protected $fillable = [
        'grant_id',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function grant()
    {
        return $this->belongsTo(Grant::class);
    }

    public function items()
    {
        return $this->hasMany(BeneficiaryListItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function hasUnresolvedFlags(): bool
    {
        return $this->items()
            ->where('duplicate_flagged', true)
            ->where('reviewed_not_duplicate', false)
            ->exists();
    }
}
