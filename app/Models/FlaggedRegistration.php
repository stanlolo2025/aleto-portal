<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlaggedRegistration extends Model
{
    protected $fillable = [
        'submitted_data',
        'matched_field',
        'matched_villager_id',
        'resolution',
        'justification',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_data' => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    public function matchedVillager()
    {
        return $this->belongsTo(VillagerRecord::class, 'matched_villager_id');
    }

    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isPending(): bool
    {
        return $this->resolution === 'pending';
    }
}
