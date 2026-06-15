<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class NinVerificationQueue extends Model
{
    protected $table = 'nin_verification_queue';

    protected $fillable = [
        'villager_record_id',
        'nin',
        'attempts',
        'status',
        'response_data',
        'next_retry_at',
    ];

    protected function casts(): array
    {
        return [
            'next_retry_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function setNinAttribute($value): void
    {
        $this->attributes['nin'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getNinAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function villagerRecord()
    {
        return $this->belongsTo(VillagerRecord::class);
    }
}
